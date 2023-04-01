<?php

// 设置GitHub API的访问令牌
$access_token = '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';

// 获取指定文件的提交历史
$file_path = 'ips';
$commit_url = 'https://api.github.com/repos/razorlabs/blacklist/commits';
$commits = array();
$page = 1;
while (count($commits) < 30) {
    $query_params = array(
        'path' => $file_path,
        'page' => $page,
        'access_token' => $access_token
    );
    $url = $commit_url . '?' . http_build_query($query_params);
    $response = file_get_contents($url);
    $result = json_decode($response, true);
    if (empty($result)) {
        break;
    }
    foreach ($result as $commit) {
        $commits[] = $commit;
        if (count($commits) >= 30) {
            break;
        }
    }
    $page++;
}

// 按提交时间从旧到新排序
usort($commits, function ($a, $b) {
    return strcmp($a['commit']['committer']['date'], $b['commit']['committer']['date']);
});

// 获取指定文件在每个历史版本中的内容
$file_url = 'https://raw.githubusercontent.com/razorlabs/blacklist/%s/ips';
$prev_file_content = '';
foreach ($commits as $commit) {
    $commit_sha = $commit['sha'];
    $commit_url = $commit['url'] . '?access_token=' . $access_token;
    $commit_response = file_get_contents($commit_url);
    $commit_result = json_decode($commit_response, true);
    $commit_date = $commit_result['commit']['committer']['date'];
    $file_content = file_get_contents(sprintf($file_url, $commit_sha));
    $new_lines = array_diff(explode("\n", $file_content), explode("\n", $prev_file_content));
    if (!empty($new_lines)) {
        echo '<p>' . $commit_date . '</p>';
        echo '<ul>';
        foreach ($new_lines as $line) {
            if (!empty($line)) {
                echo '<li>' . htmlentities($line) . '</li>';
            }
        }
        echo '</ul>';
    }
    $prev_file_content = $file_content;
}
