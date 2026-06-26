<?php
header('Location: dog-profile.php?id=' . (int) ($_GET['id'] ?? 0));
exit;
