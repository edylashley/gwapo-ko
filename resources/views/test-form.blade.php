<!DOCTYPE html>
<html>
<head>
    <title>Test Form</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body style="background: #064e3b;">
    <h1>Test Form</h1>
    
    <form id="testForm">
        <input type="text" name="test_field" value="test value" required>
        <button type="submit">Submit</button>
    </form>
    
    <div id="result"></div>
    
    <script>
        document.getElementById('testForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('test_field', document.querySelector('input[name="test_field"]').value);
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log('CSRF Token:', csrfToken);
            
            try {
                const response = await fetch('/test-form', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin',
                    body: formData
                });
                
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Success data:', data);
                    document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                } else {
                    const errorText = await response.text();
                    console.log('Error response:', errorText);
                    document.getElementById('result').innerHTML = 'Error: ' + response.status + ' - ' + errorText;
                }
            } catch (error) {
                console.error('Fetch error:', error);
                document.getElementById('result').innerHTML = 'Fetch error: ' + error.message;
            }
        });
    </script>
</body>
</html>
