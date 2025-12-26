<?php
// backend/helpers/validator.php
declare(strict_types=1);

function requireFields(array $input, array $fields): array {
  $missing = [];
  foreach ($fields as $f) {
    if (!isset($input[$f]) || trim((string)$input[$f]) === "") $missing[] = $f;
  }
  return $missing;
}

function isValidDate(string $date): bool {
  $d = DateTime::createFromFormat("Y-m-d", $date);
  return $d && $d->format("Y-m-d") === $date;
}
