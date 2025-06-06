<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LTI 1.3 Test Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            <i class="fas fa-flask text-blue-600 mr-2"></i>
                            LTI 1.3 Test Dashboard
                        </h1>
                        <p class="text-gray-600 mt-2">Test and validate LTI 1.3 integration components</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full">
                            <i class="fas fa-check-circle mr-1"></i>
                            Ready for Testing
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
            @endif

            <!-- Test Categories -->
            <div class="grid md:grid-cols-2 gap-6">

                <!-- Basic Endpoint Tests -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-network-wired text-blue-600 mr-2"></i>
                        Endpoint Tests
                    </h2>

                    <div class="space-y-3">
                        <a href="/auth/jwks" target="_blank"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div>
                                <div class="font-medium">JWKS Endpoint</div>
                                <div class="text-sm text-gray-600">Public key set for JWT verification</div>
                            </div>
                            <i class="fas fa-external-link-alt text-gray-400"></i>
                        </a>

                        <a href="/lti/config" target="_blank"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div>
                                <div class="font-medium">Tool Configuration</div>
                                <div class="text-sm text-gray-600">LTI tool configuration JSON</div>
                            </div>
                            <i class="fas fa-external-link-alt text-gray-400"></i>
                        </a>
                    </div>
                </div>

                <!-- LTI Flow Tests -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-exchange-alt text-green-600 mr-2"></i>
                        LTI Flow Tests
                    </h2>

                    <div class="space-y-3">
                        <form action="{{ route('lti.test.tool') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-between p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors border-2 border-green-200">
                                <div class="text-left">
                                    <div class="font-medium text-green-800">Test Tool Interface</div>
                                    <div class="text-sm text-green-600">Launch tool with mock LTI context</div>
                                </div>
                                <i class="fas fa-play text-green-600"></i>
                            </button>
                        </form>

                        <form action="{{ route('lti.test.oidc') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-between p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors border-2 border-blue-200">
                                <div class="text-left">
                                    <div class="font-medium text-blue-800">Simulate OIDC Flow</div>
                                    <div class="text-sm text-blue-600">Test authentication initiation</div>
                                </div>
                                <i class="fas fa-key text-blue-600"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Grade Passback Test -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-graduation-cap text-purple-600 mr-2"></i>
                        Grade Passback Test
                    </h2>

                    <form id="gradeForm" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Score</label>
                                <input type="number" name="score" value="85" min="0" max="100"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Max Score</label>
                                <input type="number" name="max_score" value="100" min="1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                            <input type="text" name="comment" value="Test grade submission"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>

                        <button type="submit"
                            class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Test Grade Passback
                        </button>
                    </form>

                    <div id="gradeResult" class="mt-4 hidden"></div>
                </div>

                <!-- Session Management -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-cogs text-orange-600 mr-2"></i>
                        Session Management
                    </h2>

                    <div class="space-y-3">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600">
                                <strong>LTI Context:</strong>
                                @if(session('lti_context'))
                                <span class="text-green-600">Active</span>
                                @else
                                <span class="text-red-600">None</span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('lti.test.clear') }}"
                            class="flex items-center justify-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors border-2 border-orange-200 text-orange-800 font-medium">
                            <i class="fas fa-trash mr-2"></i>
                            Clear Session Data
                        </a>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Testing Instructions
                </h2>

                <div class="prose text-gray-600">
                    <ol class="list-decimal list-inside space-y-2">
                        <li><strong>Start with Endpoint Tests:</strong> Verify that JWKS and configuration endpoints return valid data.</li>
                        <li><strong>Test Tool Interface:</strong> Use the mock LTI context to see how the tool renders with user/course data.</li>
                        <li><strong>Test Grade Passback:</strong> Verify that grade submission works (will show test data since this is a mock environment).</li>
                        <li><strong>For Real LTI Testing:</strong> Configure this tool in Canvas using the URLs from the LTI_SETUP.md file.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Grade passback form handler
        document.getElementById('gradeForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const resultDiv = document.getElementById('gradeResult');

            try {
                const response = await fetch('{{ route("lti.test.grade") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });

                const result = await response.json();

                resultDiv.className = result.success ?
                    'mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded' :
                    'mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded';

                resultDiv.innerHTML = `
                    <i class="fas fa-${result.success ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
                    ${result.message}
                `;
                resultDiv.classList.remove('hidden');

                setTimeout(() => {
                    resultDiv.classList.add('hidden');
                }, 5000);

            } catch (error) {
                resultDiv.className = 'mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded';
                resultDiv.innerHTML = `
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Error: ${error.message}
                `;
                resultDiv.classList.remove('hidden');
            }
        });
    </script>
</body>

</html>