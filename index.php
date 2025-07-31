<?php
// Read wordlist from file
function loadWordList($filename = 'wordlists2.txt') {
    if (!file_exists($filename)) {
        return [];
    }
    
    $content = file_get_contents($filename);
    
    // Extract words from the JavaScript array format
    preg_match_all('/"([^"]+)"/', $content, $matches);
    
    return $matches[1] ?? [];
}

// Filter words by length (for shorter, more manageable passwords)
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

// Generate password
function generatePassword($wordCount = 3, $separator = '', $addNumbers = false, $capitalizeFirst = true, $uniqueWords = true) {
    // Use filtered wordlist for shorter words
    $wordList = getFilteredWordList(8);
    
    if (empty($wordList)) {
        return 'Error: Wordlist not found';
    }
    
    $selectedWords = [];
    $availableWords = $wordList;
    
    for ($i = 0; $i < $wordCount; $i++) {
        if (empty($availableWords)) break;
        
        $randomIndex = array_rand($availableWords);
        $word = $availableWords[$randomIndex];
        $selectedWords[] = $word;
        
        if ($uniqueWords) {
            unset($availableWords[$randomIndex]);
            $availableWords = array_values($availableWords);
        }
    }
    
    // Process words
    if ($capitalizeFirst) {
        $selectedWords = array_map(function($word) {
            return ucfirst(strtolower($word));
        }, $selectedWords);
    } else {
        $selectedWords = array_map('strtolower', $selectedWords);
    }
    
    // Add numbers if requested (FIXED: only add numbers when checkbox is checked)
    if ($addNumbers === true) {
        $randomNumber = rand(0, 999);
        $insertPosition = rand(0, count($selectedWords));
        array_splice($selectedWords, $insertPosition, 0, $randomNumber);
    }
    
    return implode($separator, $selectedWords);
}

// Handle AJAX request
if (isset($_POST['action']) && $_POST['action'] === 'generate') {
    $wordCount = intval($_POST['wordCount'] ?? 3);
    $separator = $_POST['separator'] ?? '';
    $addNumbers = isset($_POST['addNumbers']) && $_POST['addNumbers'] === 'true';
    $capitalizeFirst = isset($_POST['capitalizeFirst']) && $_POST['capitalizeFirst'] === 'true';
    $uniqueWords = isset($_POST['uniqueWords']) && $_POST['uniqueWords'] === 'true';
    
    $password = generatePassword($wordCount, $separator, $addNumbers, $capitalizeFirst, $uniqueWords);
    
    header('Content-Type: application/json');
    echo json_encode(['password' => $password]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Word-Based Password Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-yellow-400 mb-2">
                    <i class="fas fa-key mr-3"></i>Word-Based Password Generator
                </h1>
                <p class="text-gray-400">Generate secure passwords using meaningful words (3-8 chars) for ~18 character passwords</p>
            </div>

            <!-- Generator Card -->
            <div class="bg-gray-800 rounded-lg p-8 shadow-xl">
                <!-- Password Display -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Generated Password</label>
                    <div class="flex items-center space-x-3">
                        <div class="flex-1 bg-gray-700 p-4 rounded-lg border border-gray-600">
                            <div id="passwordDisplay" class="text-xl font-mono text-center text-yellow-400">
                                Click "Generate Password" to start
                            </div>
                        </div>
                        <button id="copyBtn" class="bg-blue-600 hover:bg-blue-700 p-4 rounded-lg transition-colors" onclick="copyPassword()" disabled>
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <!-- Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Word Count -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Number of Words</label>
                        <div class="flex items-center space-x-3">
                            <input type="range" id="wordCountSlider" min="2" max="5" value="3" 
                                   class="flex-1 h-2 bg-gray-700 rounded-lg">
                            <span id="wordCountValue" class="text-lg font-bold text-yellow-400 w-8">3</span>
                        </div>
                    </div>

                    <!-- Separator -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Separator</label>
                        <select id="separatorSelect" class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg">
                            <option value="">No separator</option>
                            <option value="-">Hyphen (-)</option>
                            <option value="_">Underscore (_)</option>
                            <option value=".">Dot (.)</option>
                            <option value=" ">Space</option>
                        </select>
                    </div>
                </div>

                <!-- Additional Options -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Options</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="addNumbers" class="mr-2">
                            <span>Add random numbers</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="capitalizeFirst" checked class="mr-2">
                            <span>Capitalize first letter</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="uniqueWords" checked class="mr-2">
                            <span>Use unique words only</span>
                        </label>
                    </div>
                </div>

                <!-- Generate Button -->
                <div class="text-center">
                    <button id="generateBtn" onclick="generatePassword()" 
                            class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 px-8 py-3 rounded-lg font-bold text-lg">
                        <i class="fas fa-magic mr-2"></i>Generate Password
                    </button>
                </div>
            </div>

            <!-- History -->
            <div class="mt-8 bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-medium mb-3">Recent Passwords</h3>
                <div id="passwordHistory" class="space-y-2 max-h-40 overflow-y-auto">
                </div>
            </div>

            <!-- Wordlist Info -->
            <div class="mt-8 bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-medium mb-3 text-blue-400">
                    <i class="fas fa-info-circle mr-2"></i>Wordlist Information
                </h3>
                <p class="text-gray-300">
                    This generator uses a curated list of <?php echo count(getFilteredWordList(8)); ?> meaningful words 
                    (3-8 characters) to create memorable yet secure passwords around 18 characters. 
                    The words are carefully selected to be positive, inspiring, and easy to remember.
                </p>
            </div>
        </div>
    </div>

    <script>
        let currentPassword = '';
        let passwordHistory = [];

        // Update word count display
        document.getElementById('wordCountSlider').addEventListener('input', function() {
            document.getElementById('wordCountValue').textContent = this.value;
        });

        // Generate password function
        function generatePassword() {
            const wordCount = parseInt(document.getElementById('wordCountSlider').value);
            const separator = document.getElementById('separatorSelect').value;
            const addNumbers = document.getElementById('addNumbers').checked;
            const capitalizeFirst = document.getElementById('capitalizeFirst').checked;
            const uniqueWords = document.getElementById('uniqueWords').checked;

            // Show loading state
            document.getElementById('passwordDisplay').textContent = 'Generating...';
            document.getElementById('generateBtn').disabled = true;

            // Send AJAX request
            const formData = new FormData();
            formData.append('action', 'generate');
            formData.append('wordCount', wordCount);
            formData.append('separator', separator);
            formData.append('addNumbers', addNumbers);
            formData.append('capitalizeFirst', capitalizeFirst);
            formData.append('uniqueWords', uniqueWords);

            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                currentPassword = data.password;
                document.getElementById('passwordDisplay').textContent = data.password;
                document.getElementById('copyBtn').disabled = false;
                document.getElementById('generateBtn').disabled = false;
                
                // Add to history
                addToHistory(data.password);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('passwordDisplay').textContent = 'Error generating password';
                document.getElementById('generateBtn').disabled = false;
            });
        }

        // Copy password function
        function copyPassword() {
            if (!currentPassword) return;

            navigator.clipboard.writeText(currentPassword).then(function() {
                const copyBtn = document.getElementById('copyBtn');
                copyBtn.innerHTML = '<i class="fas fa-check"></i>';
                copyBtn.style.backgroundColor = '#10b981';
                
                setTimeout(() => {
                    copyBtn.innerHTML = '<i class="fas fa-copy"></i>';
                    copyBtn.style.backgroundColor = '';
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy password. Please select and copy manually.');
            });
        }

        // Add password to history
        function addToHistory(password) {
            passwordHistory = passwordHistory.filter(p => p !== password);
            passwordHistory.unshift(password);
            
            if (passwordHistory.length > 10) {
                passwordHistory = passwordHistory.slice(0, 10);
            }

            updateHistoryDisplay();
        }

        // Update history display
        function updateHistoryDisplay() {
            const historyContainer = document.getElementById('passwordHistory');
            historyContainer.innerHTML = '';

            passwordHistory.forEach((password, index) => {
                const historyItem = document.createElement('div');
                historyItem.className = 'flex items-center justify-between bg-gray-700 p-3 rounded-lg';
                historyItem.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-400">#${index + 1}</span>
                        <span class="font-mono text-sm text-yellow-400">${password}</span>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="useHistoryPassword('${password}')" 
                                class="text-blue-400 hover:text-blue-300 text-sm">
                            <i class="fas fa-arrow-up mr-1"></i>Use
                        </button>
                        <button onclick="copyHistoryPassword('${password}')" 
                                class="text-green-400 hover:text-green-300 text-sm">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    </div>
                `;
                historyContainer.appendChild(historyItem);
            });
        }

        // Use password from history
        function useHistoryPassword(password) {
            currentPassword = password;
            document.getElementById('passwordDisplay').textContent = password;
            document.getElementById('copyBtn').disabled = false;
        }

        // Copy password from history
        function copyHistoryPassword(password) {
            navigator.clipboard.writeText(password).then(function() {
                console.log('Password copied from history');
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }

        // Generate initial password on page load
        window.addEventListener('load', function() {
            generatePassword();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 'g':
                        e.preventDefault();
                        generatePassword();
                        break;
                    case 'c':
                        if (currentPassword) {
                            e.preventDefault();
                            copyPassword();
                        }
                        break;
                }
            }
        });
    </script>
</body>
</html> 