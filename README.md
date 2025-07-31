# Word-Based Password Generator

A modern, web-based password generator that creates secure, memorable passwords using meaningful words from a curated wordlist.

## 🌟 Features

- **Meaningful Passwords**: Uses 185 carefully selected positive, inspiring words
- **Customizable Options**:
  - Number of words (2-5)
  - Separators (none, hyphen, underscore, dot, space)
  - Add random numbers (optional)
  - Capitalize first letter
  - Use unique words only
- **Password Length**: ~18 characters for optimal security and memorability
- **Modern UI**: Beautiful, responsive design with Tailwind CSS
- **Password History**: Keeps track of recently generated passwords
- **Copy to Clipboard**: One-click password copying
- **Keyboard Shortcuts**: Ctrl+G to generate, Ctrl+C to copy

## 🚀 Quick Start

### Prerequisites
- XAMPP (Apache + PHP)
- Web browser

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/webased-passgen.git
   cd webased-passgen
   ```

2. **Deploy to XAMPP:**
   ```bash
   # Copy to XAMPP htdocs directory
   sudo cp -r . /opt/lampp/htdocs/webased_passgen/
   ```

3. **Start XAMPP:**
   ```bash
   sudo /opt/lampp/lampp start
   ```

4. **Access the application:**
   ```
   http://localhost/webased_passgen/
   ```

## 📁 Project Structure

```
webased_passgen/
├── index.php              # Main application file
├── wordlists2.txt         # Curated wordlist (185 words)
├── README.md              # This file
├── .gitignore            # Git ignore rules
└── test_wordlists.php    # Test script (excluded from repo)
```

## 🎯 How It Works

### Word Selection
- **Source**: 1,003 meaningful words from `wordlists2.txt`
- **Filtered**: 185 words (3-8 characters) for optimal password length
- **Examples**: Radiance, Clarity, Resolve, Ascent, Triumph, Vitality, Empower, Courage

### Password Generation
1. **Word Selection**: Randomly selects words from the filtered list
2. **Processing**: Applies capitalization and uniqueness rules
3. **Number Addition**: Optionally inserts random numbers (0-999)
4. **Assembly**: Joins words with chosen separator

### Example Passwords
- **Without numbers**: `TrueglowHoperayHeartray`
- **With numbers**: `AnchorUplift405Soulglow`
- **With separators**: `Hopelift-Calmlift-Beacon`

## 🔧 Configuration

### Wordlist Customization
Edit `wordlists2.txt` to add your own words:
```javascript
const wordList = ["YourWord1", "YourWord2", "YourWord3", ...]
```

### Styling
The application uses Tailwind CSS. Modify the classes in `index.php` to customize the appearance.

## 🛡️ Security Features

- **No Server Storage**: Passwords are generated client-side via AJAX
- **Meaningful Words**: Easier to remember than random strings
- **Configurable Length**: Adjustable word count for different security needs
- **Unique Words**: Option to prevent word repetition

## 🎨 UI Features

- **Dark Theme**: Modern dark interface
- **Responsive Design**: Works on desktop and mobile
- **Real-time Updates**: Instant password generation
- **Visual Feedback**: Loading states and copy confirmations
- **Accessibility**: Keyboard shortcuts and screen reader friendly

## ⌨️ Keyboard Shortcuts

- **Ctrl+G**: Generate new password
- **Ctrl+C**: Copy current password

## 🔄 API Endpoints

### POST /index.php
Generates a password based on form parameters:

**Parameters:**
- `action`: "generate"
- `wordCount`: Number of words (2-5)
- `separator`: Separator character
- `addNumbers`: "true" or not set
- `capitalizeFirst`: "true" or not set
- `uniqueWords`: "true" or not set

**Response:**
```json
{
  "password": "GeneratedPassword123"
}
```

## 🧪 Testing

Run the test script to verify functionality:
```bash
php test_wordlists.php
```

## 📊 Statistics

- **Total Words**: 1,003
- **Filtered Words**: 185 (3-8 characters)
- **Average Password Length**: ~18 characters
- **Word Categories**: Positive, inspiring, memorable

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 🙏 Acknowledgments

- **Wordlist**: Curated collection of positive, meaningful words
- **UI Framework**: Tailwind CSS for beautiful styling
- **Icons**: Font Awesome for enhanced user experience

## 📞 Support

For issues, questions, or contributions, please open an issue on GitHub.

---

**Made with ❤️ for secure, memorable passwords** 