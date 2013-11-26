<?php
//setlocale(LC_MONETARY, 'en_US');
$o = money_format('$%#10n', $input);

return $o;