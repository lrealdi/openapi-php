<?php

namespace erasys\OpenApi\Spec\v3;

/**
 * Defines a security scheme that can be used by the operations. Supported schemes are HTTP authentication, an API key
 * (either as a header or as a query parameter), OAuth2's common flows (implicit, password, application and access
 * code) as defined in RFC6749, and OpenID Connect Discovery.
 *
 * @see https://swagger.io/specification/#securitySchemeObject
 * @see https://tools.ietf.org/html/rfc6749
 * @see https://tools.ietf.org/html/draft-ietf-oauth-discovery-06
 */
class SecurityScheme extends AbstractObject implements ExtensibleInterface
{
    /**
     * REQUIRED. The type of the security scheme. Valid values are "apiKey", "http", "oauth2", "openIdConnect".
     *
     * Applies To: all
     *
     * @var string
     */
    public $type;

    /**
     * A short description for security scheme. CommonMark syntax MAY be used for rich text representation.
     *
     * Applies To: all
     *
     * @var string
     */
    public $description;

    /**
     * REQUIRED. The name of the header, query or cookie parameter to be used.
     *
     * Applies To: apiKey
     *
     * @var string
     */
    public $name;

    /**
     * REQUIRED. The location of the API key. Valid values are "query", "header" or "cookie".
     *
     * Applies To: apiKey
     *
     * @var string
     */
    public $in;

    /**
     * REQUIRED. The name of the HTTP Authorization scheme to be used in the Authorization header as defined in RFC7235.
     *
     * Applies To: http
     *
     * @var string
     */
    public $scheme;

    /**
     * A hint to the client to identify how the bearer token is formatted. Bearer tokens are usually generated by an
     * authorization server, so this information is primarily for documentation purposes.
     *
     * Applies To: http ("bearer")
     *
     * @var string
     */
    public $bearerFormat;

    /**
     * REQUIRED. An object containing configuration information for the flow types supported.
     *
     * Applies To: oauth2
     *
     * @var OauthFlows
     */
    public $flows;

    /**
     * REQUIRED. OpenId Connect URL to discover OAuth2 configuration values. This MUST be in the form of a URL.
     *
     * Applies To: openIdConnect
     *
     * @var string
     */
    public $openIdConnectUrl;

    /**
     * @param string      $type
     * @param string|null $description
     * @param array       $additionalProperties
     */
    public function __construct($type, $description = null, array $additionalProperties = [])
    {
        parent::__construct($additionalProperties);
        $this->type        = $type;
        $this->description = $description;
    }
}
