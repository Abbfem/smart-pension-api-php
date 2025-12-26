<?php

declare(strict_types=1);

namespace NestPension\Utils;

use DOMDocument;
use DOMElement;
use SimpleXMLElement;
use NestPension\Exceptions\NestException;

/**
 * XML processing utilities for NEST Pension API.
 * Handles XML serialization and deserialization with NEST namespaces.
 */
class XmlProcessor
{
    // NEST XML Namespaces
    public const NAMESPACE_ADDRESS = 'http://www.ws.nestpensions.org.uk/ns/Address';
    public const NAMESPACE_COMMON_TYPES = 'http://www.ws.nestpensions.org.uk/ns/CommonTypes';
    public const NAMESPACE_EMPLOYER = 'http://www.ws.nestpensions.org.uk/ns/Employer';
    public const NAMESPACE_GROUP = 'http://www.ws.nestpensions.org.uk/ns/Group';
    public const NAMESPACE_MEMBER = 'http://www.ws.nestpensions.org.uk/ns/Member';
    public const NAMESPACE_MESSAGE = 'http://www.ws.nestpensions.org.uk/ns/Message';
    public const NAMESPACE_PERSON = 'http://www.ws.nestpensions.org.uk/ns/Person';
    public const NAMESPACE_PAYMENT_SOURCE = 'http://www.ws.nestpensions.org.uk/ns/PaymentSource';
    public const NAMESPACE_SCHEDULE = 'http://www.ws.nestpensions.org.uk/ns/Schedule';
    public const NAMESPACE_CONTRIBUTION_RATES = 'http://www.ws.nestpensions.org.uk/ns/ContributionRates';
    public const NAMESPACE_ERROR_RESPONSE = 'http://www.ws.nestpensions.org.uk/ns/ErrorResponse';
    public const NAMESPACE_RESPONSE = 'http://www.ws.nestpensions.org.uk/ns/Response';

    // Operation-specific namespaces
    public const NAMESPACE_ENROL_WORKERS_REQUEST = 'http://www.ws.nestpensions.org.uk/ns/EnrolWorkersRequest';
    public const NAMESPACE_ENROL_WORKERS_RESPONSE = 'http://www.ws.nestpensions.org.uk/ns/EnrolWorkersResponse';
    public const NAMESPACE_UPDATE_CONTRIBUTIONS_REQUEST = 'http://www.ws.nestpensions.org.uk/ns/UpdateContributionsRequest';
    public const NAMESPACE_UPDATE_CONTRIBUTIONS_RESPONSE = 'http://www.ws.nestpensions.org.uk/ns/UpdateContributionsResponse';
    public const NAMESPACE_APPROVE_PAYMENT_REQUEST = 'http://www.ws.nestpensions.org.uk/ns/ApprovePaymentRequest';
    public const NAMESPACE_APPROVE_PAYMENT_RESPONSE = 'http://www.ws.nestpensions.org.uk/ns/ApprovePaymentResponse';
    public const NAMESPACE_SETUP_EMPLOYER_REQUEST = 'http://www.ws.nestpensions.org.uk/ns/SetupEmployerRequest';
    public const NAMESPACE_SETUP_EMPLOYER_RESPONSE = 'http://www.ws.nestpensions.org.uk/ns/SetupEmployerResponse';

    private array $namespaceMap = [
        'add' => self::NAMESPACE_ADDRESS,
        'com' => self::NAMESPACE_COMMON_TYPES,
        'emp' => self::NAMESPACE_EMPLOYER,
        'grp' => self::NAMESPACE_GROUP,
        'mem' => self::NAMESPACE_MEMBER,
        'msg' => self::NAMESPACE_MESSAGE,
        'per' => self::NAMESPACE_PERSON,
        'pms' => self::NAMESPACE_PAYMENT_SOURCE,
        'sch' => self::NAMESPACE_SCHEDULE,
        'cnr' => self::NAMESPACE_CONTRIBUTION_RATES,
        'err' => self::NAMESPACE_ERROR_RESPONSE,
        'res' => self::NAMESPACE_RESPONSE,
    ];

    /**
     * Create a new DOM document with proper NEST XML structure.
     */
    public function createDocument(string $rootElementName, string $rootNamespace, string $rootPrefix = ''): DOMDocument
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        // Create root element with namespace
        $rootElement = $doc->createElementNS($rootNamespace, ($rootPrefix ? $rootPrefix . ':' : '') . $rootElementName);
        $doc->appendChild($rootElement);

        // Add common NEST namespaces
        $this->addCommonNamespaces($rootElement);

        // Add XSI namespace for schema location
        $rootElement->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        return $doc;
    }

    /**
     * Add common NEST namespaces to an element.
     */
    private function addCommonNamespaces(DOMElement $element): void
    {
        foreach ($this->namespaceMap as $prefix => $namespace) {
            $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $prefix, $namespace);
        }
    }

    /**
     * Parse XML string into SimpleXMLElement with namespace handling.
     */
    public function parseXml(string $xmlString): SimpleXMLElement
    {
        // Remove BOM if present
        $xmlString = $this->removeBom($xmlString);

        // Load XML with error handling
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString);

        if ($xml === false) {
            $errors = libxml_get_errors();
            $errorMessages = array_map(fn($error) => trim($error->message), $errors);
            libxml_clear_errors();
            throw new NestException('Invalid XML format: ' . implode(', ', $errorMessages));
        }

        return $xml;
    }

    /**
     * Convert SimpleXMLElement to array with namespace handling.
     */
    public function xmlToArray(SimpleXMLElement $xml): array
    {
        $namespaces = $xml->getNamespaces(true);
        return $this->convertXmlNode($xml, $namespaces);
    }

    /**
     * Convert array to XML string.
     */
    public function arrayToXml(array $data, string $rootElement, string $rootNamespace, string $rootPrefix = ''): string
    {
        $doc = $this->createDocument($rootElement, $rootNamespace, $rootPrefix);
        $rootNode = $doc->documentElement;

        $this->arrayToXmlRecursive($data, $rootNode, $doc);

        return $doc->saveXML();
    }

    /**
     * Add schema location to XML document.
     */
    public function addSchemaLocation(DOMDocument $doc, string $namespace, string $schemaFile): void
    {
        $rootElement = $doc->documentElement;
        $rootElement->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            $namespace . ' ' . $schemaFile
        );
    }

    /**
     * Validate XML against XSD schema (if available).
     */
    public function validateXml(string $xmlString, ?string $xsdPath = null): bool
    {
        if (!$xsdPath || !file_exists($xsdPath)) {
            // Basic XML validation
            try {
                $this->parseXml($xmlString);
                return true;
            } catch (NestException $e) {
                return false;
            }
        }

        $doc = new DOMDocument();
        $doc->loadXML($xmlString);

        return $doc->schemaValidate($xsdPath);
    }

    /**
     * Extract error information from XML error response.
     */
    public function extractErrors(SimpleXMLElement $xml): array
    {
        $errors = [];
        $namespaces = $xml->getNamespaces(true);

        // Register namespaces
        foreach ($namespaces as $prefix => $namespace) {
            $xml->registerXPathNamespace($prefix ?: 'default', $namespace);
        }

        // Try different error paths based on NEST error response format
        $errorPaths = [
            '//err:Messages/err:Message',
            '//msg:Messages/msg:Message',
            '//Messages/Message',
            '//*[local-name()="Message"]'
        ];

        foreach ($errorPaths as $path) {
            $errorNodes = $xml->xpath($path);
            if ($errorNodes) {
                foreach ($errorNodes as $errorNode) {
                    $errors[] = [
                        'code' => (string)($errorNode->Code ?? $errorNode['code'] ?? ''),
                        'message' => (string)($errorNode->Description ?? $errorNode ?? ''),
                        'severity' => (string)($errorNode->Severity ?? 'error')
                    ];
                }
                break;
            }
        }

        return $errors;
    }

    /**
     * Remove BOM from XML string.
     */
    private function removeBom(string $xmlString): string
    {
        $bom = "\xef\xbb\xbf";
        if (substr($xmlString, 0, 3) === $bom) {
            return substr($xmlString, 3);
        }
        return $xmlString;
    }

    /**
     * Convert XML node to array recursively.
     */
    private function convertXmlNode(SimpleXMLElement $node, array $namespaces): array
    {
        $result = [];
        $nodeName = $node->getName();

        // Handle attributes
        $attributes = [];
        foreach ($node->attributes() as $attr => $value) {
            $attributes[$attr] = (string)$value;
        }

        // Handle namespace attributes
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($node->attributes($namespace) as $attr => $value) {
                $attributes[($prefix ? $prefix . ':' : '') . $attr] = (string)$value;
            }
        }

        // Handle child elements
        $children = [];
        foreach ($node->children() as $childName => $child) {
            $children[] = $this->convertXmlNode($child, $namespaces);
        }

        // Handle namespaced children
        foreach ($namespaces as $prefix => $namespace) {
            if ($prefix !== '') {
                foreach ($node->children($namespace) as $childName => $child) {
                    $children[] = $this->convertXmlNode($child, $namespaces);
                }
            }
        }

        $result[$nodeName] = [
            'value' => (string)$node,
            'attributes' => $attributes,
            'children' => $children
        ];

        return $result;
    }

    /**
     * Convert array to XML recursively.
     */
    private function arrayToXmlRecursive(array $data, DOMElement $parent, DOMDocument $doc): void
    {
        foreach ($data as $key => $value) {
            // Skip numeric keys for arrays
            if (is_numeric($key)) {
                if (is_array($value)) {
                    $this->arrayToXmlRecursive($value, $parent, $doc);
                }
                continue;
            }

            if (is_array($value)) {
                if (isset($value['@namespace'])) {
                    // Handle namespaced elements
                    $element = $doc->createElementNS($value['@namespace'], $key);
                    unset($value['@namespace']);
                } else {
                    $element = $doc->createElement($key);
                }

                $parent->appendChild($element);

                // Handle attributes
                if (isset($value['@attributes'])) {
                    foreach ($value['@attributes'] as $attrName => $attrValue) {
                        $element->setAttribute($attrName, (string)$attrValue);
                    }
                    unset($value['@attributes']);
                }

                // Handle text content
                if (isset($value['@value'])) {
                    $element->appendChild($doc->createTextNode((string)$value['@value']));
                    unset($value['@value']);
                }

                // Process remaining children
                $this->arrayToXmlRecursive($value, $element, $doc);
            } else {
                $element = $doc->createElement($key);
                $element->appendChild($doc->createTextNode((string)$value));
                $parent->appendChild($element);
            }
        }
    }

    /**
     * Get namespace map.
     */
    public function getNamespaceMap(): array
    {
        return $this->namespaceMap;
    }

    /**
     * Register additional namespace.
     */
    public function registerNamespace(string $prefix, string $namespace): void
    {
        $this->namespaceMap[$prefix] = $namespace;
    }
}
