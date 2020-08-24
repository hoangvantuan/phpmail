<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use PhpImap\Imap;
use PhpImap\Mailbox;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function index()
    {
        // Create PhpImap\Mailbox instance for all further actions
        $mailbox = new Mailbox(
            '{api459x.com:993/imap/ssl}INBOX', // IMAP server and mailbox folder
            'EMAIL HERE!!!!!!!!!!!!!!!!', // Username for the before configured mailbox
            'PASSWORD HERE!!!!!!!!!!!!!!', // Password for the before configured username
            null, // Directory, where attachments will be saved (optional)
            'UTF-8' // Server encoding (optional)
        );

// set some connection arguments (if appropriate)
        $mailbox->setConnectionArgs(
            CL_EXPUNGE // expunge deleted mails upon mailbox close
            | OP_SECURE // don't do non-secure authentication
        );

        try {
            // Get all emails (messages)
            // PHP.net imap_search criteria: http://php.net/manual/en/function.imap-search.php
            $mailsIds = $mailbox->searchMailbox('ALL');
        } catch (PhpImap\Exceptions\ConnectionException $ex) {
            echo "IMAP connection failed: " . $ex;
            die();
        }

// If $mailsIds is empty, no emails could be found
        if (!$mailsIds) {
            die('Mailbox is empty');
        }

// Get the first message
// If '__DIR__' was defined in the first line, it will automatically
// save all attachments to the specified directory
        $mail = $mailbox->getMail($mailsIds[0]);

// Show, if $mail has one or more attachments
        echo "\nMail has attachments? ";
        if ($mail->hasAttachments()) {
            echo "Yes\n";
        } else {
            echo "No\n";
        }

// Print all information of $mail
        print_r($mail);

// Print all attachements of $mail
        echo "\n\nAttachments:\n";
        print_r($mail->getAttachments());
    }
}
