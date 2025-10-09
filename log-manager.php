<?php
/**
 * Log Management System
 * GDPR Compliant Log Handling
 */

class LogManager {
    private $logFile;
    private $maxSize;
    private $retentionDays;
    
    public function __construct($logFile, $maxSize = '10MB', $retentionDays = 365) {
        $this->logFile = $logFile;
        $this->maxSize = $this->parseSize($maxSize);
        $this->retentionDays = $retentionDays;
        
        // Create logs directory if it doesn't exist
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    public function writeLog($data) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 200),
            'data' => $data
        ];
        
        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
        
        // Check file size and rotate if necessary
        if (file_exists($this->logFile) && filesize($this->logFile) > $this->maxSize) {
            $this->rotateLog();
        }
        
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        // Clean old logs
        $this->cleanOldLogs();
    }
    
    public function rotateLog() {
        if (!file_exists($this->logFile)) {
            return;
        }
        
        $timestamp = date('Y-m-d_H-i-s');
        $rotatedFile = $this->logFile . '.' . $timestamp;
        
        rename($this->logFile, $rotatedFile);
        
        // Compress old log file
        if (function_exists('gzopen')) {
            $gzFile = $rotatedFile . '.gz';
            $fp_in = fopen($rotatedFile, 'rb');
            $fp_out = gzopen($gzFile, 'wb9');
            
            while (!feof($fp_in)) {
                gzwrite($fp_out, fread($fp_in, 1024 * 512));
            }
            
            fclose($fp_in);
            gzclose($fp_out);
            unlink($rotatedFile);
        }
    }
    
    public function cleanOldLogs() {
        $logDir = dirname($this->logFile);
        $cutoffTime = time() - ($this->retentionDays * 24 * 60 * 60);
        
        $files = glob($logDir . '/*');
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                unlink($file);
            }
        }
    }
    
    public function getLogs($limit = 100, $offset = 0) {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $logs = [];
        
        foreach ($lines as $line) {
            $log = json_decode($line, true);
            if ($log) {
                $logs[] = $log;
            }
        }
        
        // Sort by timestamp (newest first)
        usort($logs, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return array_slice($logs, $offset, $limit);
    }
    
    public function getStats() {
        $logs = $this->getLogs(1000); // Get last 1000 entries for stats
        
        $stats = [
            'total_submissions' => count($logs),
            'successful_submissions' => 0,
            'failed_submissions' => 0,
            'spam_attempts' => 0,
            'rate_limit_hits' => 0,
            'submissions_by_day' => [],
            'submissions_by_hour' => [],
            'top_ips' => [],
            'top_user_agents' => []
        ];
        
        $ipCounts = [];
        $userAgentCounts = [];
        
        foreach ($logs as $log) {
            $data = $log['data'];
            
            if ($data['success']) {
                $stats['successful_submissions']++;
            } else {
                $stats['failed_submissions']++;
                
                if (strpos($data['error'], 'Spam') !== false) {
                    $stats['spam_attempts']++;
                }
                
                if (strpos($data['error'], 'Rate limit') !== false) {
                    $stats['rate_limit_hits']++;
                }
            }
            
            // Count by day
            $day = date('Y-m-d', strtotime($log['timestamp']));
            $stats['submissions_by_day'][$day] = ($stats['submissions_by_day'][$day] ?? 0) + 1;
            
            // Count by hour
            $hour = date('H', strtotime($log['timestamp']));
            $stats['submissions_by_hour'][$hour] = ($stats['submissions_by_hour'][$hour] ?? 0) + 1;
            
            // Count IPs
            $ip = $log['ip'];
            $ipCounts[$ip] = ($ipCounts[$ip] ?? 0) + 1;
            
            // Count User Agents
            $ua = $log['user_agent'];
            $userAgentCounts[$ua] = ($userAgentCounts[$ua] ?? 0) + 1;
        }
        
        // Sort and get top 10
        arsort($ipCounts);
        $stats['top_ips'] = array_slice($ipCounts, 0, 10, true);
        
        arsort($userAgentCounts);
        $stats['top_user_agents'] = array_slice($userAgentCounts, 0, 10, true);
        
        return $stats;
    }
    
    private function parseSize($size) {
        $size = trim($size);
        $last = strtolower($size[strlen($size)-1]);
        $size = (int) $size;
        
        switch($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }
        
        return $size;
    }
    
    // GDPR Compliance: Delete logs older than retention period
    public function purgeOldLogs() {
        $this->cleanOldLogs();
    }
    
    // GDPR Compliance: Delete specific user data
    public function deleteUserData($email) {
        if (!file_exists($this->logFile)) {
            return true;
        }
        
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $newLines = [];
        
        foreach ($lines as $line) {
            $log = json_decode($line, true);
            if ($log && isset($log['data']['email']) && $log['data']['email'] !== $email) {
                $newLines[] = $line;
            }
        }
        
        file_put_contents($this->logFile, implode("\n", $newLines) . "\n", LOCK_EX);
        return true;
    }
}
?>
