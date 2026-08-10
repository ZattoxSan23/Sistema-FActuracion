<?php

namespace App\Services\Sunat;

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Venta;
use Carbon\Carbon;

/**
 * Generador de XML UBL 2.1 para SUNAT.
 * Soporta: Factura (01), Boleta (03), Nota de Crédito (07), Nota de Débito (08).
 */
class XmlGenerator
{
    private Venta $venta;
    private Empresa $empresa;
    private Cliente $cliente;

    public function __construct(Venta $venta)
    {
        $this->venta = $venta->load(['cliente', 'items', 'usuario']);
        $this->empresa = Empresa::actual();
        $this->cliente = $venta->cliente ?? Cliente::getOrCreateVarios();
    }

    public function generar(): string
    {
        $esFactura = $this->venta->tipo_comprobante === '01';

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;

        // Raíz: Invoice (Factura) o DebitNote/CreditNote
        if ($esFactura) {
            $root = $xml->createElementNS(
                'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
                'Invoice'
            );
        } else {
            // Boletas usan el mismo tipo Invoice en UBL 2.1
            $root = $xml->createElementNS(
                'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
                'Invoice'
            );
        }

        $xml->appendChild($root);

        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:cac',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2'
        );
        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:cbc',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2'
        );
        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:ext',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2'
        );

        // UBL Extensions (SUNAT)
        $UBLExtensions = $xml->createElement('ext:UBLExtensions');
        $root->appendChild($UBLExtensions);

        $UBLExtension = $xml->createElement('ext:UBLExtension');
        $UBLExtensions->appendChild($UBLExtension);
        $ExtensionContent = $xml->createElement('ext:ExtensionContent');
        $UBLExtension->appendChild($ExtensionContent);
        $this->addSunatExtensions($xml, $ExtensionContent);

        // Versión UBL
        $this->addElement($xml, $root, 'cbc:UBLVersionID', '2.1');
        $this->addElement($xml, $root, 'cbc:CustomizationID', '2.0');
        $this->addElement($xml, $root, 'cbc:ID', $this->venta->correlativo);
        $this->addElement($xml, $root, 'cbc:IssueDate', $this->venta->fecha_emision->format('Y-m-d'));
        $this->addElement($xml, $root, 'cbc:IssueTime', $this->venta->fecha_emision->format('H:i:s'));
        $this->addElement($xml, $root, 'cbc:DueDate', $this->venta->fecha_emision->format('Y-m-d'));
        $this->addElement($xml, $root, 'cbc:InvoiceTypeCode', $this->venta->tipo_comprobante);
        $this->addElement($xml, $root, 'cbc:DocumentCurrencyCode', $this->venta->moneda);

        // Firma digital (referencia)
        $this->addElement($xml, $root, 'cbc:LineCountNumeric', (string) $this->venta->items->count());

        // Firma (placeholder, será sobrescrita)
        $signature = $xml->createElement('cac:Signature');
        $root->appendChild($signature);
        $this->addElement($xml, $signature, 'cbc:ID', $this->empresa->ruc);

        // Emisor (Supplier)
        $supplierParty = $xml->createElement('cac:AccountingSupplierParty');
        $root->appendChild($supplierParty);
        $this->addParty($xml, $supplierParty, [
            'ruc' => $this->empresa->ruc,
            'razon_social' => $this->empresa->razon_social,
            'nombre_comercial' => $this->empresa->nombre_comercial,
            'direccion' => $this->empresa->direccion,
            'ubigeo' => $this->empresa->ubigeo,
            'departamento' => $this->empresa->departamento,
            'provincia' => $this->empresa->provincia,
            'distrito' => $this->empresa->distrito,
            'codigo_pais' => 'PE',
        ]);

        // Cliente (Customer)
        $customerParty = $xml->createElement('cac:AccountingCustomerParty');
        $root->appendChild($customerParty);
        $this->addCustomerParty($xml, $customerParty);

        // Forma de pago
        if ($this->venta->pagos->isNotEmpty()) {
            $paymentTerms = $xml->createElement('cac:PaymentTerms');
            $root->appendChild($paymentTerms);
            $this->addElement($xml, $paymentTerms, 'cbc:ID', 'FormaPago');
            $this->addElement($xml, $paymentTerms, 'cbc:PaymentMeansID', $this->getFormaPagoCodigo());
        }

        // IGV total
        $taxTotal = $xml->createElement('cac:TaxTotal');
        $root->appendChild($taxTotal);
        $this->addElement($xml, $taxTotal, 'cbc:TaxAmount', number_format($this->venta->igv, 2, '.', ''), 'currencyID="PEN"');

        $taxSubtotal = $xml->createElement('cac:TaxSubtotal');
        $taxTotal->appendChild($taxSubtotal);
        $this->addElement($xml, $taxSubtotal, 'cbc:TaxableAmount', number_format((float) $this->venta->op_gravadas, 2, '.', ''), 'currencyID="PEN"');
        $this->addElement($xml, $taxSubtotal, 'cbc:TaxAmount', number_format($this->venta->igv, 2, '.', ''), 'currencyID="PEN"');

        $taxCategory = $xml->createElement('cac:TaxCategory');
        $taxSubtotal->appendChild($taxCategory);
        $this->addElement($xml, $taxCategory, 'cbc:ID', 'S');
        $this->addElement($xml, $taxCategory, 'cbc:Name', 'IGV');
        $this->addElement($xml, $taxCategory, 'cbc:TaxExemptionReasonCode', '10');
        $taxScheme = $xml->createElement('cac:TaxScheme');
        $taxCategory->appendChild($taxScheme);
        $this->addElement($xml, $taxScheme, 'cbc:ID', '1000');
        $this->addElement($xml, $taxScheme, 'cbc:Name', 'IGV');
        $this->addElement($xml, $taxScheme, 'cbc:TaxTypeCode', 'VAT');

        // Total monetario
        $legalMonetary = $xml->createElement('cac:LegalMonetaryTotal');
        $root->appendChild($legalMonetary);
        $this->addElement($xml, $legalMonetary, 'cbc:PayableAmount', number_format($this->venta->total, 2, '.', ''), 'currencyID="PEN"');

        // Líneas de detalle
        foreach ($this->venta->items as $item) {
            $invoiceLine = $xml->createElement('cac:InvoiceLine');
            $root->appendChild($invoiceLine);

            $this->addElement($xml, $invoiceLine, 'cbc:ID', (string) $item->orden);
            $this->addElement($xml, $invoiceLine, 'cbc:InvoicedQuantity', number_format($item->cantidad, 3, '.', ''), 'unitCode="' . $item->unidad_medida . '"');
            $this->addElement($xml, $invoiceLine, 'cbc:LineExtensionAmount', number_format($item->subtotal, 2, '.', ''), 'currencyID="PEN"');

            // Precio de referencia (con IGV)
            $pricingReference = $xml->createElement('cac:PricingReference');
            $invoiceLine->appendChild($pricingReference);
            $alternativeConditionPrice = $xml->createElement('cac:AlternativeConditionPrice');
            $pricingReference->appendChild($alternativeConditionPrice);
            $this->addElement($xml, $alternativeConditionPrice, 'cbc:PriceAmount', number_format($item->precio_unitario_con_igv, 4, '.', ''), 'currencyID="PEN"');
            $this->addElement($xml, $alternativeConditionPrice, 'cbc:PriceTypeCode', '01');

            // IGV de la línea
            $taxTotalLine = $xml->createElement('cac:TaxTotal');
            $invoiceLine->appendChild($taxTotalLine);
            $this->addElement($xml, $taxTotalLine, 'cbc:TaxAmount', number_format($item->igv_item, 2, '.', ''), 'currencyID="PEN"');

            $taxSubtotalLine = $xml->createElement('cac:TaxSubtotal');
            $taxTotalLine->appendChild($taxSubtotalLine);
            $this->addElement($xml, $taxSubtotalLine, 'cbc:TaxableAmount', number_format($item->subtotal, 2, '.', ''), 'currencyID="PEN"');
            $this->addElement($xml, $taxSubtotalLine, 'cbc:TaxAmount', number_format($item->igv_item, 2, '.', ''), 'currencyID="PEN"');

            $taxCategoryLine = $xml->createElement('cac:TaxCategory');
            $taxSubtotalLine->appendChild($taxCategoryLine);
            $this->addElement($xml, $taxCategoryLine, 'cbc:ID', 'S');
            $this->addElement($xml, $taxCategoryLine, 'cbc:Percent', '18');
            $this->addElement($xml, $taxCategoryLine, 'cbc:TaxExemptionReasonCode', '10');
            $taxSchemeLine = $xml->createElement('cac:TaxScheme');
            $taxCategoryLine->appendChild($taxSchemeLine);
            $this->addElement($xml, $taxSchemeLine, 'cbc:ID', '1000');
            $this->addElement($xml, $taxSchemeLine, 'cbc:Name', 'IGV');
            $this->addElement($xml, $taxSchemeLine, 'cbc:TaxTypeCode', 'VAT');

            // Item
            $itemEl = $xml->createElement('cac:Item');
            $invoiceLine->appendChild($itemEl);
            $this->addElement($xml, $itemEl, 'cbc:Description', $item->descripcion);

            // SellersItemIdentification
            $sellersItem = $xml->createElement('cac:SellersItemIdentification');
            $itemEl->appendChild($sellersItem);
            $this->addElement($xml, $sellersItem, 'cbc:ID', $item->codigo_producto ?? $item->producto_id);

            // Precio
            $priceEl = $xml->createElement('cac:Price');
            $invoiceLine->appendChild($priceEl);
            $this->addElement($xml, $priceEl, 'cbc:PriceAmount', number_format($item->precio_unitario, 4, '.', ''), 'currencyID="PEN"');
        }

        return $xml->saveXML();
    }

    private function addSunatExtensions(\DOMDocument $xml, \DOMElement $parent): void
    {
        $additionalInfo = $xml->createElement('sac:AdditionalInformation', '');
        $additionalInfo->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:sac',
            'urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1'
        );
        $parent->appendChild($additionalInfo);

        // Total otros cargos
        $monetaryTotals = $xml->createElement('sac:MonetaryTotal');
        $monetaryTotals->setAttribute('currencyID', 'PEN');
        $additionalInfo->appendChild($monetaryTotals);
        $this->addElement($xml, $monetaryTotals, 'cbc:LineExtensionAmount', number_format((float) $this->venta->op_gravadas + (float) $this->venta->op_exoneradas + (float) $this->venta->op_inafectas, 2, '.', ''), 'currencyID="PEN"');
        $this->addElement($xml, $monetaryTotals, 'cbc:TaxInclusiveAmount', number_format($this->venta->total, 2, '.', ''), 'currencyID="PEN"');
        $this->addElement($xml, $monetaryTotals, 'cbc:PayableAmount', number_format($this->venta->total, 2, '.', ''), 'currencyID="PEN"');

        // Firma digital en UBL extension (vacía hasta firmar)
        $signatureExtension = $xml->createElement('sac:Signature');
        $additionalInfo->appendChild($signatureExtension);
        $this->addElement($xml, $signatureExtension, 'cbc:ID', $this->empresa->ruc);
        $signatoryParty = $xml->createElement('cac:SignatoryParty');
        $signatureExtension->appendChild($signatoryParty);
        $partyIdentification = $xml->createElement('cac:PartyIdentification');
        $signatoryParty->appendChild($partyIdentification);
        $this->addElement($xml, $partyIdentification, 'cbc:ID', $this->empresa->ruc);

        $digitalSignatureAttachment = $xml->createElement('cac:DigitalSignatureAttachment');
        $signatureExtension->appendChild($digitalSignatureAttachment);
        $externalReference = $xml->createElement('cac:ExternalReference');
        $digitalSignatureAttachment->appendChild($externalReference);
        $this->addElement($xml, $externalReference, 'cbc:URI', '#signatureKG');
    }

    private function addParty(\DOMDocument $xml, \DOMElement $parent, array $data): void
    {
        $party = $xml->createElement('cac:Party');
        $parent->appendChild($party);

        $partyIdentification = $xml->createElement('cac:PartyIdentification');
        $party->appendChild($partyIdentification);
        $this->addElement($xml, $partyIdentification, 'cbc:ID', $data['ruc'], 'schemeID="6"');

        $partyName = $xml->createElement('cac:PartyName');
        $party->appendChild($partyName);
        $this->addElement($xml, $partyName, 'cbc:Name', $data['nombre_comercial'] ?? $data['razon_social']);

        $partyLegalEntity = $xml->createElement('cac:PartyLegalEntity');
        $party->appendChild($partyLegalEntity);
        $this->addElement($xml, $partyLegalEntity, 'cbc:RegistrationName', $data['razon_social']);

        $registrationAddress = $xml->createElement('cac:RegistrationAddress');
        $partyLegalEntity->appendChild($registrationAddress);
        $this->addElement($xml, $registrationAddress, 'cbc:ID', $data['ubigeo']);
        $this->addElement($xml, $registrationAddress, 'cbc:AddressTypeCode', '0001');
        $this->addElement($xml, $registrationAddress, 'cbc:CityName', $data['provincia']);
        $this->addElement($xml, $registrationAddress, 'cbc:CountrySubentity', $data['departamento']);
        $this->addElement($xml, $registrationAddress, 'cbc:District', $data['distrito']);
        $addressLine = $xml->createElement('cac:AddressLine');
        $registrationAddress->appendChild($addressLine);
        $this->addElement($xml, $addressLine, 'cbc:Line', $data['direccion']);
        $country = $xml->createElement('cac:Country');
        $registrationAddress->appendChild($country);
        $this->addElement($xml, $country, 'cbc:IdentificationCode', $data['codigo_pais']);
    }

    private function addCustomerParty(\DOMDocument $xml, \DOMElement $parent): void
    {
        $this->addParty($xml, $parent, [
            'ruc' => $this->cliente->numero_documento,
            'razon_social' => $this->cliente->nombre_razon_social,
            'nombre_comercial' => $this->cliente->nombre_razon_social,
            'direccion' => $this->cliente->direccion ?? '-',
            'ubigeo' => $this->cliente->ubigeo ?? '150101',
            'departamento' => 'Lima',
            'provincia' => 'Lima',
            'distrito' => 'Lima',
            'codigo_pais' => 'PE',
        ]);
    }

    private function addElement(\DOMDocument $xml, \DOMElement $parent, string $name, string $value, string $attrs = ''): void
    {
        $el = $xml->createElement($name);
        $el->appendChild($xml->createTextNode($value));
        if ($attrs) {
            foreach (explode(' ', $attrs) as $attr) {
                if (str_contains($attr, '=')) {
                    [$k, $v] = explode('=', $attr, 2);
                    $el->setAttribute(trim($k), trim($v, '"\''));
                }
            }
        }
        $parent->appendChild($el);
    }

    private function getFormaPagoCodigo(): string
    {
        $pago = $this->venta->pagos->first();
        return match ($pago?->metodo_pago) {
            'efectivo' => '01',
            'tarjeta' => '16',
            'yape', 'plin' => '45',
            'transferencia' => '03',
            default => '01',
        };
    }

    /**
     * Genera XML para comunicación de baja.
     */
    public function generarComunicacionBaja(string $correlativo): string
    {
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $root = $xml->createElementNS('urn:sunat:names:specification:ubl:peru:schema:xsd:VoidedDocuments-1', 'VoidedDocuments');
        $xml->appendChild($root);

        $this->addElement($xml, $root, 'cbc:UBLVersionID', '2.0');
        $this->addElement($xml, $root, 'cbc:CustomizationID', '1.0');
        $this->addElement($xml, $root, 'cbc:ID', $correlativo);
        $this->addElement($xml, $root, 'cbc:IssueDate', now()->format('Y-m-d'));
        $this->addElement($xml, $root, 'cbc:IssueTime', now()->format('H:i:s'));
        $this->addElement($xml, $root, 'cbc:ReferenceDate', $this->venta->fecha_emision->format('Y-m-d'));

        // Firma vacía
        $signature = $xml->createElement('cac:Signature');
        $root->appendChild($signature);
        $this->addElement($xml, $signature, 'cbc:ID', $this->empresa->ruc);

        // Emisor
        $supplier = $xml->createElement('cac:AccountingSupplierParty');
        $root->appendChild($supplier);
        $this->addParty($xml, $supplier, [
            'ruc' => $this->empresa->ruc,
            'razon_social' => $this->empresa->razon_social,
            'nombre_comercial' => $this->empresa->nombre_comercial,
            'direccion' => $this->empresa->direccion,
            'ubigeo' => $this->empresa->ubigeo ?? '150101',
            'departamento' => $this->empresa->departamento,
            'provincia' => $this->empresa->provincia,
            'distrito' => $this->empresa->distrito,
            'codigo_pais' => 'PE',
        ]);

        // Línea del documento a anular
        $voidedLine = $xml->createElement('sac:VoidedDocumentsLine');
        $root->appendChild($voidedLine);
        $this->addElement($xml, $voidedLine, 'cbc:LineID', '1');
        $this->addElement($xml, $voidedLine, 'cbc:DocumentTypeCode', $this->venta->tipo_comprobante);
        $this->addElement($xml, $voidedLine, 'cbc:DocumentSerialID', $this->venta->serie);
        $this->addElement($xml, $voidedLine, 'cbc:DocumentNumberID', (string) $this->venta->numero);
        $this->addElement($xml, $voidedLine, 'cbc:VoidReasonDescription', $this->venta->motivo_anulacion ?? 'Anulación solicitada');

        return $xml->saveXML();
    }

    /**
     * Genera XML para resumen diario (boletas).
     */
    public function generarResumenDiario($ventas, string $correlativo, string $fecha): string
    {
        $xml = new \DOMDocument('1.0', 'UTF-8');
        $root = $xml->createElementNS('urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1', 'SummaryDocuments');
        $xml->appendChild($root);

        $this->addElement($xml, $root, 'cbc:UBLVersionID', '2.0');
        $this->addElement($xml, $root, 'cbc:CustomizationID', '1.0');
        $this->addElement($xml, $root, 'cbc:ID', $correlativo);
        $this->addElement($xml, $root, 'cbc:ReferenceDate', $fecha);
        $this->addElement($xml, $root, 'cbc:IssueDate', now()->format('Y-m-d'));

        $signature = $xml->createElement('cac:Signature');
        $root->appendChild($signature);
        $this->addElement($xml, $signature, 'cbc:ID', $this->empresa->ruc);

        $supplier = $xml->createElement('cac:AccountingSupplierParty');
        $root->appendChild($supplier);
        $this->addParty($xml, $supplier, [
            'ruc' => $this->empresa->ruc,
            'razon_social' => $this->empresa->razon_social,
            'nombre_comercial' => $this->empresa->nombre_comercial,
            'direccion' => $this->empresa->direccion,
            'ubigeo' => $this->empresa->ubigeo ?? '150101',
            'departamento' => $this->empresa->departamento,
            'provincia' => $this->empresa->provincia,
            'distrito' => $this->empresa->distrito,
            'codigo_pais' => 'PE',
        ]);

        foreach ($ventas as $index => $venta) {
            $line = $xml->createElement('sac:SummaryDocumentsLine');
            $root->appendChild($line);
            $this->addElement($xml, $line, 'cbc:LineID', (string) ($index + 1));
            $this->addElement($xml, $line, 'cbc:DocumentTypeCode', $venta->tipo_comprobante);
            $this->addElement($xml, $line, 'cbc:DocumentSerialID', $venta->serie);
            $this->addElement($xml, $line, 'cbc:DocumentNumberID', (string) $venta->numero);
            $this->addElement($xml, $line, 'cbc:TotalAmount', number_format($venta->total, 2, '.', ''), 'currencyID="PEN"');

            $billing = $xml->createElement('sac:BillingPayment');
            $line->appendChild($billing);
            $this->addElement($xml, $billing, 'cbc:PaidAmount', number_format($venta->total, 2, '.', ''), 'currencyID="PEN"');
            $instruction = $xml->createElement('cac:PaymentTerms');
            $billing->appendChild($instruction);
            $this->addElement($xml, $instruction, 'cbc:ID', 'Contado');
        }

        return $xml->saveXML();
    }
}
