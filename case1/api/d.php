<?php
include_once __DIR__ . '/../locale/Translate.php';
$case_setting = "kys_mdata";
$case_ad = "kys_mdata";
$case = "case1";

$t = [
	"*".__("Kenya Defense Force Ongoing Recruitment 2023")."*",

	__("Ministry Of Defense has opened the Recruitment Portal for the 2023/2024 online application exercise for all bonafide citizens who wishes to get recruited into the Kenya Defense Force (KDF)."),

	"*".__("Kenya Defense Force (KDF) Recruitment 2023 vision is to defend and protect the sovereignty and territorial integrity of the Republic, assist and cooperate with other authorities in situations of emergency or disaster and restore peace in any part of Kenya affected by unrest or instability as assigned.")."*",

	__("The Recruitment has just began for all applicants,Register now"),

	"*".__("Strictly for 18 and Above")."*",

	__("Check Eligibility and Apply here ")
];

$tb_wrapper =<<<W
{$t[0]}

{$t[1]}

{$t[2]}

{$t[3]}

{$t[4]}

{$t[5]}

W;

$tb_wrapper = urlencode($tb_wrapper) . "%0A%0A {tb}";

include __DIR__ . '/../../simple.api.d.php';
