<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LTI Error - Code Comprehension Tool</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            line-height: 1.6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .error-container {
            max-width: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
        }

        .error-header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 40px 30px;
        }

        .error-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .error-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 300;
        }

        .error-content {
            padding: 40px 30px;
        }

        .error-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .error-instructions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin-bottom: 30px;
            text-align: left;
        }

        .error-instructions h3 {
            margin: 0 0 15px;
            color: #333;
            font-size: 1.1rem;
        }

        .error-instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #666;
        }

        .error-instructions li {
            margin-bottom: 8px;
        }

        .support-info {
            font-size: 0.9rem;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-header">
            <div class="error-icon">⚠️</div>
            <h1>LTI Access Required</h1>
        </div>

        <div class="error-content">
            <div class="error-message">
                {{ $message ?? 'This tool requires LTI context to function properly.' }}
            </div>

            <div class="error-instructions">
                <h3>How to access this tool:</h3>
                <ol>
                    <li>Go to your Learning Management System (Canvas, Blackboard, etc.)</li>
                    <li>Navigate to the course where this tool is installed</li>
                    <li>Click on the "Code Comprehension" tool link</li>
                    <li>The tool will launch with proper LTI context</li>
                </ol>
            </div>

            <div class="support-info">
                If you continue to experience issues, please contact your instructor or system administrator.
            </div>
        </div>
    </div>
</body>

</html>