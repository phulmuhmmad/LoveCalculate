<?php
$yourName = '';
$partnerName = '';
$loveScore = '--';
$yourShare = '--';
$partnerShare = '--';
$posted = false;

function safeStringLength($value) {
    return mb_strlen($value, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $yourName = trim($_POST['yourName'] ?? '');
    $partnerName = trim($_POST['partnerName'] ?? '');

    if ($yourName !== '' && $partnerName !== '') {
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

        $posted = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Love Calculator</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <canvas id="particleCanvas"></canvas>

  <div class="page">
    <div class="container <?php echo $posted ? 'hidden' : 'main-content'; ?>" id="mainContent">
      <h1>Love Calculator</h1>
      <p>Enter your name and your partner's name, then click Calculate.</p>

      <form method="post" action="result.php" class="form-layout">
        <div class="form-row">
          <label for="yourName">Your Name</label>
          <input type="text" id="yourName" name="yourName" value="<?php echo htmlspecialchars($yourName, ENT_QUOTES); ?>" placeholder="Enter your name" required />
        </div>

        <div class="form-row">
          <label for="partnerName">Partner Name</label>
          <input type="text" id="partnerName" name="partnerName" value="<?php echo htmlspecialchars($partnerName, ENT_QUOTES); ?>" placeholder="Enter partner's name" required />
        </div>

        <button type="submit" id="calculateBtn">Calculate</button>
      </form>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
