<?php
//quicksort
function quicksort($array) {
	if (count($array) <= 1) {
		return $array;
	}
	$pivot = $array[0];
	$less = $greater = [];
	for ($i = 1; $i < count($array); $i++) {
		if ($array[$i] < $pivot) {
			$less[] = $array[$i];
		} else {
			$greater[] = $array[$i];
		}
	}
	return array_merge(quicksort($less), [$pivot], quicksort($greater));
}
