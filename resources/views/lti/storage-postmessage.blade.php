<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LTI Platform Storage</title>
</head>

<body>
    <script>
        // LTI Platform Storage PostMessage API
        // This handles communication between Canvas and our tool for Safari compatibility

        let storage = {};

        window.addEventListener('message', function(event) {
            // Verify origin for security
            const allowedOrigins = [
                'https://canvas.test.instructure.com',
                'https://sso.test.canvaslms.com',
                '{{ config("app.url") }}'
            ];

            if (!allowedOrigins.some(origin => event.origin.startsWith(origin))) {
                console.warn('LTI Storage: Message from unauthorized origin:', event.origin);
                return;
            }

            const data = event.data;

            if (data.subject === 'lti.put_data') {
                // Store data
                storage[data.key] = data.value;

                // Send success response
                event.source.postMessage({
                    subject: 'lti.put_data.response',
                    key: data.key,
                    success: true
                }, event.origin);

                console.log('LTI Storage: Data stored for key:', data.key);

            } else if (data.subject === 'lti.get_data') {
                // Retrieve data
                const value = storage[data.key] || null;

                // Send response with data
                event.source.postMessage({
                    subject: 'lti.get_data.response',
                    key: data.key,
                    value: value,
                    success: true
                }, event.origin);

                console.log('LTI Storage: Data retrieved for key:', data.key);

                // Remove data after retrieval (one-time use)
                if (value !== null) {
                    delete storage[data.key];
                }

            } else {
                console.warn('LTI Storage: Unknown message subject:', data.subject);
            }
        });

        // Signal that we're ready to receive messages
        if (window.parent !== window) {
            window.parent.postMessage({
                subject: 'lti.storage_ready'
            }, '*');
        }

        console.log('LTI Platform Storage API ready');
    </script>

    <div style="display: none;">
        LTI Platform Storage API - This page handles cross-origin storage for Safari compatibility
    </div>
</body>

</html>