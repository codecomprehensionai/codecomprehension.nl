<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Comprehension Tool</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }

        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .content {
            padding: 40px;
        }

        .code-section {
            margin: 30px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .code-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            font-weight: 600;
            color: #495057;
        }

        .code-input {
            width: 100%;
            min-height: 200px;
            padding: 20px;
            border: none;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 14px;
            line-height: 1.5;
            background: #f8f9fa;
            color: #333;
            resize: vertical;
        }

        .code-input:focus {
            outline: none;
            background: #fff;
        }

        .analysis-section {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px 10px 0;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background: #0056b3;
        }

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #1e7e34;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .info-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .info-card h3 {
            margin: 0 0 15px;
            color: #333;
            font-size: 1.3rem;
        }

        .info-card p {
            margin: 5px 0;
            color: #666;
        }

        .info-card strong {
            color: #333;
        }

        .features {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 30px;
        }

        .features h2 {
            margin: 0 0 20px;
            color: #333;
            text-align: center;
        }

        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #28a745;
        }

        .feature-list li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            margin-right: 10px;
        }

        .debug-info {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .debug-info h3 {
            margin: 0 0 15px;
            color: #6c757d;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .debug-info pre {
            background: #ffffff;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border: 1px solid #e9ecef;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Code Comprehension Tool</h1>
            <p>LTI 1.3 Integration Active</p>
        </div>

        <div class="content">
            <div class="info-grid">
                <div class="info-card">
                    <h3>User Information</h3>
                    <p><strong>Name:</strong> {{ $userInfo['name'] ?? 'Not available' }}</p>
                    <p><strong>Email:</strong> {{ $userInfo['email'] ?? 'Not available' }}</p>
                    <p><strong>Roles:</strong> {{ implode(', ', $roles) ?: 'Not available' }}</p>
                    @if($canAdminister)
                    <p><strong>Status:</strong> <span style="color: #28a745;">Administrator/Instructor</span></p>
                    @endif
                </div>

                <div class="info-card">
                    <h3>Course Information</h3>
                    <p><strong>Course:</strong> {{ $courseInfo['title'] ?? $courseInfo['label'] ?? 'Not available' }}</p>
                    <p><strong>Course ID:</strong> {{ $courseInfo['id'] ?? 'Not available' }}</p>
                    <p><strong>Resource:</strong> {{ $resourceLinkInfo['title'] ?? 'Not available' }}</p>
                </div>

                <div class="info-card">
                    <h3>Platform Information</h3>
                    <p><strong>Platform:</strong> {{ $platformInfo['issuer'] ?? 'Not available' }}</p>
                    <p><strong>LTI Version:</strong> {{ $platformInfo['version'] ?? 'Not available' }}</p>
                    <p><strong>Message Type:</strong> {{ $platformInfo['message_type'] ?? 'Not available' }}</p>
                    @if($supportsAgs)
                    <p><strong>Grade Passback:</strong> <span style="color: #28a745;">Supported</span></p>
                    @endif
                </div>
            </div>

            <div class="features">
                <h2>Available Features</h2>
                <ul class="feature-list">
                    <li>Code analysis and comprehension</li>
                    <li>Assignment integration</li>
                    <li>Grade passback support</li>
                    <li>Student progress tracking</li>
                    <li>Real-time feedback</li>
                    <li>Multi-language support</li>
                </ul>
            </div>

            @if(config('app.debug'))
            <div class="debug-info">
                <h3>Debug Information</h3>
                <pre>{{ json_encode($ltiContext, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif
        </div>
    </div>
</body>

</html>