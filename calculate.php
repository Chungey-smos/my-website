<?php
// Function to convert number to English words
function numberToEnglishWords($number) {
    $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine");
    $teens = array("Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen");
    $tens = array("", "Ten", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety");
    
    if ($number == 0) return "Zero";
    if ($number < 0) return "Negative " . numberToEnglishWords(abs($number));
    
    $words = "";
    
    if (($number / 1000000) >= 1) {
        $words .= numberToEnglishWords(floor($number / 1000000)) . " Million ";
        $number %= 1000000;
    }
    
    if (($number / 1000) >= 1) {
        $words .= numberToEnglishWords(floor($number / 1000)) . " Thousand ";
        $number %= 1000;
    }
    
    if (($number / 100) >= 1) {
        $words .= numberToEnglishWords(floor($number / 100)) . " Hundred ";
        $number %= 100;
    }
    
    if ($number > 0) {
        if (!empty($words)) $words .= "and ";
        
        if ($number < 10) {
            $words .= $ones[$number];
        } elseif ($number < 20) {
            $words .= $teens[$number - 10];
        } else {
            $words .= $tens[floor($number / 10)];
            if (($number % 10) > 0) {
                $words .= " " . $ones[$number % 10];
            }
        }
    }
    
    return trim($words);
}

// Function to convert number to Khmer words
function numberToKhmerWords($number) {
    $khmerOnes = array("", "មួយ", "ពីរ", "បី", "បួន", "ប្រាំ", "ប្រាំមួយ", "ប្រាំពីរ", "ប្រាំបី", "ប្រាំបួន");
    $khmerTens = array("", "ដប់", "ម្ភៃ", "សាមសិប", "សែសិប", "ហាសិប", "ហុកសិប", "ចិតសិប", "ប៉ែតសិប", "កៅសិប");
    $khmerTeens = array("ដប់", "ដប់មួយ", "ដប់ពីរ", "ដប់បី", "ដប់បួន", "ដប់ប្រាំ", "ដប់ប្រាំមួយ", "ដប់ប្រាំពីរ", "ដប់ប្រាំបី", "ដប់ប្រាំបួន");
    
    if ($number == 0) return "សូន្យ";
    if ($number < 0) return "ដក " . numberToKhmerWords(abs($number));
    
    $words = "";
    
    // Handle millions
    if (($number / 1000000) >= 1) {
        $words .= numberToKhmerWords(floor($number / 1000000)) . "លាន";
        $number %= 1000000;
        if ($number > 0) $words .= " ";
    }
    
    // Handle hundred thousands
    if (($number / 100000) >= 1) {
        $words .= $khmerOnes[floor($number / 100000)] . "សែន";
        $number %= 100000;
        if ($number > 0) $words .= " ";
    }
    
    // Handle ten thousands
    if (($number / 10000) >= 1) {
        $words .= $khmerOnes[floor($number / 10000)] . "ម៉ឺន";
        $number %= 10000;
        if ($number > 0) $words .= " ";
    }
    
    // Handle thousands
    if (($number / 1000) >= 1) {
        $words .= $khmerOnes[floor($number / 1000)] . "ពាន់";
        $number %= 1000;
        if ($number > 0) $words .= " ";
    }
    
    // Handle hundreds
    if (($number / 100) >= 1) {
        $words .= $khmerOnes[floor($number / 100)] . "រយ";
        $number %= 100;
        if ($number > 0) $words .= " ";
    }
    
    // Handle tens and ones
    if ($number > 0) {
        if ($number < 10) {
            $words .= $khmerOnes[$number];
        } elseif ($number < 20) {
            $words .= $khmerTeens[$number - 10];
        } else {
            $words .= $khmerTens[floor($number / 10)];
            if (($number % 10) > 0) {
                $words .= $khmerOnes[$number % 10];
            }
        }
    }
    
    return $words;
}

// Save data to text file
function saveToFile($riel, $english, $khmer, $dollars) {
    $filename = "conversions.txt";
    $data = date('Y-m-d H:i:s') . "\t" . $riel . "\t" . $english . "\t" . $khmer . "\t" . $dollars . "\n";
    file_put_contents($filename, $data, FILE_APPEND);
}

// Process form submission
$error = "";
$result = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $riel = isset($_POST["riel"]) ? trim($_POST["riel"]) : "";
    
    if (empty($riel)) {
        $error = "Please enter a number";
    } elseif (!is_numeric($riel)) {
        $error = "Please enter a valid number";
    } elseif ($riel < 0) {
        $error = "Please enter a positive number";
    } else {
        $riel = (int)$riel;
        $english = numberToEnglishWords($riel) . " Riel";
        $khmer = numberToKhmerWords($riel) . "រៀល";
        $dollars = number_format($riel / 4000, 2) . "$";
        
        saveToFile($riel, $english, $khmer, $dollars);
        
        $result = array(
            "riel" => $riel,
            "english" => $english,
            "khmer" => $khmer,
            "dollars" => $dollars
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riel to Dollar Converter</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>NUMBERS TO WORDS CALCULATOR</h1>
        <p>1 dollar = 4000 riel</p>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="riel">Please input your data (Riel):</label>
                <input type="number" id="riel" name="riel" value="<?php echo isset($_POST["riel"]) ? htmlspecialchars($_POST["riel"]) : ''; ?>" min="0" step="1" required>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <button type="submit">Convert</button>
        </form>
        
        <?php if (!empty($result)): ?>
            <div class="result">
                <h2>Conversion Result:</h2>
                <p><strong>a.</strong> <?php echo $result["english"]; ?></p>
                <p><strong>b.</strong> <?php echo $result["khmer"]; ?></p>
                <p><strong>c.</strong> <?php echo $result["dollars"]; ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>