<?php
$yourName = '';
$partnerName = '';
$loveScore = '--';
$yourShare = '--';
$partnerShare = '--';
$error = '';

function safeStringLength($value) {
    return mb_strlen($value, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $yourName = trim($_POST['yourName'] ?? '');
    $partnerName = trim($_POST['partnerName'] ?? '');

    if ($yourName === '' || $partnerName === '') {
        $error = 'Both names are required to calculate the love score.';
    } else {
        $combined = mb_strtolower($yourName . $partnerName, 'UTF-8');
        $totalScore = 0;
        for ($i = 0; $i < mb_strlen($combined, 'UTF-8'); $i++) {
            $char = mb_substr($combined, $i, 1, 'UTF-8');
            $bytes = unpack('C*', $char);
            foreach ($bytes as $byte) {
                $totalScore += $byte;
            }
        }

        $loveScore = $totalScore % 101;
        $yourShare = (int) round(($loveScore * safeStringLength($yourName)) / (safeStringLength($yourName) + safeStringLength($partnerName)));
        $partnerShare = max(0, $loveScore - $yourShare);

        $csvFile = __DIR__ . '/love_data.csv';
        $isNewFile = !file_exists($csvFile);

        if ($fp = fopen($csvFile, 'a')) {
            if ($isNewFile) {
                fputcsv($fp, ['Date', 'Your Name', 'Partner Name', 'Love Score', 'Your Share', 'Partner Share']);
            }
            fputcsv($fp, [date('Y-m-d H:i:s'), $yourName, $partnerName, $loveScore, $yourShare, $partnerShare]);
            fclose($fp);
        }
    }
} else {
    $error = 'Please submit the form from the Love Calculator page.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Love Calculator Result</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <canvas id="particleCanvas"></canvas>

  <div class="page">
    <div class="container">
      <?php if ($error === '') : ?>
        <h1>Love Calculator Result</h1>
        <p style="text-align:center; color:#d7cadc; margin-bottom:24px;">Here is your love score and shares.</p>

        <div class="heart-display">
          <div class="name-side left-side">
            <span><?php echo htmlspecialchars($yourName, ENT_QUOTES); ?></span>
            <span class="share-value"><?php echo htmlspecialchars($yourShare, ENT_QUOTES); ?>%</span>
          </div>

          <div class="heart-center">
            <div class="heart-icon <?php
                if ($loveScore >= 80) {
                    echo 'love-high';
                } elseif ($loveScore >= 50) {
                    echo 'love-medium';
                } elseif ($loveScore >= 30) {
                    echo 'love-low';
                } else {
                    echo 'love-weak';
                }
            ?>">❤️</div>
            <div class="love-value"><?php echo htmlspecialchars($loveScore, ENT_QUOTES); ?>%</div>
          </div>

          <div class="name-side right-side">
            <span><?php echo htmlspecialchars($partnerName, ENT_QUOTES); ?></span>
            <span class="share-value"><?php echo htmlspecialchars($partnerShare, ENT_QUOTES); ?>%</span>
          </div>
        </div>

        <a class="secondary-btn" href="index.php">Try Again</a>
      <?php else : ?>
        <h1>Oops!</h1>
        <p style="text-align:center; color:#d7cadc; margin-bottom:24px;"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></p>
        <a class="secondary-btn" href="index.php">Go Back</a>
      <?php endif; ?>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
