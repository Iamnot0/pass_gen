<?php
// Load wordlist from JavaScript array format
function loadWordList() {
    if (!file_exists('wordlists2.txt')) {
        return [];
    }
    
    $content = file_get_contents('wordlists2.txt');
    
    // Extract words from the JavaScript array format
    preg_match_all('/"([^"]+)"/', $content, $matches);
    
    return $matches[1] ?? [];
}

// Get filtered wordlist (3-8 characters, alphabetic only)
function getFilteredWordList($maxLength = 8) {
    $wordList = loadWordList();
    $filteredWords = [];

    foreach ($wordList as $word) {
        $cleanWord = strtolower($word);
        // Only include words that are 3-8 characters long
        if (strlen($cleanWord) >= 3 && strlen($cleanWord) <= $maxLength) {
            $filteredWords[] = $word;
        }
    }
    return $filteredWords;
}

// Generate password: 2 words + 1 digit
function generatePassword() {
    $wordList = getFilteredWordList();
    
    if (empty($wordList)) {
        return "Error: No words available";
    }
    
    // Select 2 random words
    $word1 = $wordList[array_rand($wordList)];
    $word2 = $wordList[array_rand($wordList)];
    
    // Generate 1 random digit
    $digit = rand(0, 9);
    
    // Combine: 2 words + 1 digit
    $password = $word1 . $word2 . $digit;
    
    return $password;
}

// Handle AJAX requests
if (isset($_POST['action']) && $_POST['action'] === 'generate') {
    $password = generatePassword();
    echo json_encode(['password' => $password]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Generator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5em;
            font-weight: 300;
        }

        .password-display {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            margin: 30px 0;
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
            word-break: break-all;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .password-display:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .generate-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.2em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .generate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .generate-btn:active {
            transform: translateY(0);
        }

        .copy-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .copy-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }

        .copy-btn:active {
            transform: translateY(0);
        }

        .copy-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 10px;
            margin: 10px 0;
            display: none;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 10px;
            }

            h1 {
                font-size: 2em;
            }

            .password-display {
                font-size: 1.2em;
                padding: 15px;
            }

            .generate-btn, .copy-btn {
                padding: 12px 25px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Password Generator</h1>
        
        <div class="password-display" id="passwordDisplay">
            Click "Generate Password" to start
        </div>

        <div>
            <button class="generate-btn" id="generateBtn" onclick="generatePassword()">
                🔄 Generate Password
            </button>
            <button class="copy-btn" id="copyBtn" onclick="copyPassword()" disabled>
                📋 Copy to Clipboard
            </button>
        </div>

        <div class="success-message" id="successMessage">
            ✅ Password copied to clipboard!
        </div>
    </div>

    <script>
        let currentPassword = '';

        // Generate password
        function generatePassword() {
            const generateBtn = document.getElementById('generateBtn');
            const passwordDisplay = document.getElementById('passwordDisplay');
            
            generateBtn.classList.add('loading');
            generateBtn.textContent = '🔄 Generating...';

            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=generate'
            })
            .then(response => response.json())
            .then(data => {
                currentPassword = data.password;
                passwordDisplay.textContent = currentPassword;
                
                // Enable copy button
                document.getElementById('copyBtn').disabled = false;
                
                // Reset button
                generateBtn.classList.remove('loading');
                generateBtn.textContent = '🔄 Generate Password';
            })
            .catch(error => {
                console.error('Error:', error);
                passwordDisplay.textContent = 'Error generating password';
                generateBtn.classList.remove('loading');
                generateBtn.textContent = '🔄 Generate Password';
            });
        }

        // Copy password to clipboard
        function copyPassword() {
            if (currentPassword) {
                navigator.clipboard.writeText(currentPassword).then(() => {
                    showSuccessMessage();
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = currentPassword;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    showSuccessMessage();
                });
            }
        }

        // Show success message
        function showSuccessMessage() {
            const successMessage = document.getElementById('successMessage');
            successMessage.style.display = 'block';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 2000);
        }

        // Generate initial password on page load
        window.addEventListener('load', function() {
            generatePassword();
        });
    </script>
</body>
</html> 
