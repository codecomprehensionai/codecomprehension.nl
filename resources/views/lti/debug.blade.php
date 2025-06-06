<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LTI Debug Information</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007cba;
        }

        .error-badge {
            background: #dc3545;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            display: inline-block;
            margin-bottom: 10px;
        }

        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #007cba;
        }

        .section h3 {
            margin-top: 0;
            color: #007cba;
        }

        .param-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 10px;
            align-items: start;
        }

        .param-key {
            font-weight: bold;
            color: #495057;
        }

        .param-value {
            font-family: Monaco, 'Courier New', monospace;
            background: white;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            word-break: break-all;
        }

        .missing {
            color: #dc3545;
            font-style: italic;
        }

        .present {
            color: #28a745;
        }

        .help-section {
            background: #e3f2fd;
            border-color: #2196f3;
            margin-top: 30px;
        }

        .help-section h3 {
            color: #1976d2;
        }

        .required-params {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }

        pre {
            background: #f1f3f4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 0.9em;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }

        .btn:hover {
            background: #005a87;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #545b62;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="error-badge">LTI Debug Information</div>
            <h1>LTI 1.3 Parameter Analysis</h1>
            <p>Analyzing the current request to help debug LTI integration issues</p>
        </div>

        <div class="section">
            <h3>📋 Request Overview</h3>
            <div class="param-grid">
                <div class="param-key">Method:</div>
                <div class="param-value">{{ $data['method'] ?? 'Unknown' }}</div>

                <div class="param-key">URL:</div>
                <div class="param-value">{{ $data['url'] ?? 'Unknown' }}</div>

                <div class="param-key">Timestamp:</div>
                <div class="param-value">{{ $timestamp ?? now() }}</div>
            </div>
        </div>

        <div class="section">
            <h3>🔑 Required LTI Parameters</h3>
            <div class="required-params">
                <strong>For OIDC Initiation, these parameters are required:</strong>
                <ul>
                    <li><code>iss</code> - Issuer identifier</li>
                    <li><code>login_hint</code> - User login hint</li>
                    <li><code>client_id</code> - OAuth2 client identifier</li>
                    <li><code>lti_message_hint</code> - LTI message hint (recommended)</li>
                    <li><code>target_link_uri</code> - Target URI for the tool</li>
                </ul>
            </div>

            @php
            $allParams = array_merge($data['get_params'] ?? [], $data['post_params'] ?? []);
            $requiredParams = ['iss', 'login_hint', 'client_id', 'lti_message_hint', 'target_link_uri'];
            @endphp

            <div class="param-grid">
                @foreach($requiredParams as $param)
                <div class="param-key">{{ $param }}:</div>
                <div class="param-value {{ isset($allParams[$param]) ? 'present' : 'missing' }}">
                    @if(isset($allParams[$param]))
                    {{ $allParams[$param] }}
                    @else
                    <span class="missing">Missing</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        @if(!empty($data['get_params']))
        <div class="section">
            <h3>📥 GET Parameters</h3>
            <div class="param-grid">
                @foreach($data['get_params'] as $key => $value)
                <div class="param-key">{{ $key }}:</div>
                <div class="param-value">{{ is_array($value) ? json_encode($value) : $value }}</div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($data['post_params']))
        <div class="section">
            <h3>📤 POST Parameters</h3>
            <div class="param-grid">
                @foreach($data['post_params'] as $key => $value)
                <div class="param-key">{{ $key }}:</div>
                <div class="param-value">{{ is_array($value) ? json_encode($value) : Str::limit($value, 100) }}</div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($data['session']))
        <div class="section">
            <h3>🗂 Session Data</h3>
            <div class="param-grid">
                @foreach($data['session'] as $key => $value)
                <div class="param-key">{{ $key }}:</div>
                <div class="param-value">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : $value }}</div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="help-section section">
            <h3>🛠 Troubleshooting Tips</h3>

            <h4>Missing lti_message_hint:</h4>
            <p>The <code>lti_message_hint</code> parameter is typically provided by the LMS during OIDC initiation. If it's missing:</p>
            <ul>
                <li>Check your Canvas Developer Key configuration</li>
                <li>Ensure the launch is happening from within Canvas, not directly</li>
                <li>Verify the tool is properly installed in Canvas</li>
            </ul>

            <h4>Canvas Configuration URLs:</h4>
            <pre>OIDC Initiation URL: {{ config('app.url') }}/auth/oidc
Target Link URI: {{ config('app.url') }}/
Public JWK URL: {{ config('app.url') }}/auth/jwks
Redirect URIs: {{ config('app.url') }}/auth/launch</pre>

            <h4>Test Actions:</h4>
            <a href="{{ route('lti.test.dashboard') }}" class="btn">Test Dashboard</a>
            <a href="{{ route('lti.jwks') }}" class="btn btn-secondary">View JWKS</a>
            <a href="{{ route('lti.config') }}" class="btn btn-secondary">Tool Config</a>
        </div>
    </div>
</body>

</html>