<?php
    // Function to format the date
    function format_date($dateString) {
        $date = new DateTime($dateString);
        return $date->format('M, jS Y');
    }

    // Function to format status
    function format_status($status) {
        // Replace underscores and convert to uppercase
        return ucwords(str_replace('_', ' ', $status));
    }

    // Function to determine status class
    function format_status_class($status) {
        switch ($status) {
            case 'failed':
                return 'failed';
            case 'passed':
            case 'completed':
                return 'passed';
            case 'not_started':
                return 'not-started';
            case 'in_progress':
                return 'in-progress';
            default:
                return '';
        }
    }
?>