<?php

use App\Models\Prize;

$current_probability = floatval(Prize::sum('probability'));
$remaining_probability = 100 - $current_probability;

?>

@if ($remaining_probability > 0 && $remaining_probability <= 100)
<div class="alert alert-danger">
    <strong></strong> Sum of prize probability must be 100%. Currently, it's {{ $current_probability }}%. You need to add {{ $remaining_probability }}% to the prize.
</div>

@endif
