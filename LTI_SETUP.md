# LTI 1.3 Integration Setup

This application includes LTI 1.3 (Learning Tools Interoperability) integration for seamless integration with Learning Management Systems like Canvas, Blackboard, and others.

## Quick Start

1. **Generate LTI Keys**
   ```bash
   php artisan lti:generate-keys
   ```

2. **Configure Environment Variables**
   Copy the LTI variables from `.env.example` to your `.env` file and configure them:
   ```env
   LTI_CANVAS_CLIENT_ID=your_client_id_from_canvas
   LTI_CANVAS_AUTH_LOGIN_URL=https://canvas.test.instructure.com/api/lti/authorize_redirect
   LTI_CANVAS_AUTH_TOKEN_URL=https://canvas.test.instructure.com/login/oauth2/token
   LTI_CANVAS_KEY_SET_URL=https://canvas.test.instructure.com/api/lti/security/jwks
   LTI_CANVAS_DEPLOYMENT_ID=1
   ```

## Tool Configuration

Use this JSON configuration when registering the tool in your LMS:

```json
{
  "title": "Code Comprehension",
  "description": "Code comprehension",
  "target_link_uri": "https://your-domain.com/",
  "oidc_initiation_url": "https://your-domain.com/auth/oidc",
  "oidc_initiation_urls": {},
  "public_jwk_url": "https://your-domain.com/auth/jwks",
  "public_jwk": {},
  "custom_fields": {},
  "scopes": [
    "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem",
    "https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly"
  ],
  "extensions": [
    {
      "domain": "",
      "tool_id": "",
      "privacy_level": "public",
      "platform": "canvas.test.instructure.com",
      "settings": {
        "placements": [
          {
            "placement": "assignment_selection",
            "message_type": "LtiResourceLinkRequest"
          }
        ]
      }
    }
  ]
}
```

## Available Endpoints

- **OIDC Initiation**: `GET/POST /auth/oidc`
- **LTI Launch**: `POST /auth/launch`
- **Public JWK Set**: `GET /auth/jwks`
- **Tool Interface**: `GET /lti`
- **Tool Configuration**: `GET /lti/config`

## Canvas Setup Instructions

1. **Go to Canvas Admin Settings**
   - Navigate to Admin → Developer Keys
   - Click "Create Developer Key" → "LTI Key"

2. **Configure the Key**
   - **Key Name**: Code Comprehension Tool
   - **Owner Email**: your-admin@domain.com
   - **Redirect URIs**: `https://your-domain.com/auth/launch`
   - **Method**: Manual Entry
   - **Title**: Code Comprehension
   - **Target Link URI**: `https://your-domain.com/`
   - **OpenID Connect Initiation Url**: `https://your-domain.com/auth/oidc`
   - **JWK Method**: Public JWK URL
   - **Public JWK URL**: `https://your-domain.com/auth/jwks`

3. **Configure Placements**
   - Enable "Assignment Selection"
   - Set Message Type to "LtiResourceLinkRequest"

4. **Set Scopes** (if available)
   - `https://purl.imsglobal.org/spec/lti-ags/scope/lineitem`
   - `https://purl.imsglobal.org/spec/lti-ags/scope/lineitem.readonly`

5. **Save and Deploy**
   - Save the developer key
   - Turn it "ON"
   - Note the Client ID for your environment configuration

## Security Considerations

- RSA keys are stored in `storage/app/private/` and `storage/app/public/`
- Private keys have restricted permissions (600)
- All LTI communications use JWT tokens
- CSRF protection is enabled for all routes
- Session validation prevents replay attacks

## Troubleshooting

1. **"LTI context required" error**
   - Ensure you're launching from an LMS, not accessing directly
   - Check that session storage is working

2. **"Invalid state parameter" error**
   - Check that your domain matches the configured redirect URI
   - Ensure sessions are persisting between requests

3. **JWT validation errors**
   - Verify the public JWK URL is accessible
   - Check that your RSA keys were generated correctly

4. **Platform not found**
   - Verify the client_id in your LTI configuration
   - Check that the issuer matches your LMS

## Development and Testing

For development, you can access the tool configuration at:
```
GET /lti/config
```

This will return the JSON configuration needed for LMS registration.

## Support

For issues with LTI integration:
1. Check the Laravel logs in `storage/logs/laravel.log`
2. Verify your environment configuration
3. Test the JWK endpoint accessibility
4. Confirm your LMS configuration matches the tool requirements
