<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Email;

class FetchingEmailProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		/*
		$this->fetchEmails();
		*/
	}

	public function fetchEmails() {

		$hostname = env('MAIL_RECEIVING_PROTOCOL');
		$username = env('MAIL_RECEIVING_EMAIL');
		$password = env('MAIL_RECEIVING_PASSWORD');
		$email_model = new Email;


		$imap = imap_open($hostname, $username, $password);

		if ($imap === false) {
    		die("Error: Could not connect to IMAP server.\n");
		}

		$emails = imap_search($imap, 'UNSEEN');  

		if ($emails) {
    		rsort($emails);  
    		foreach ($emails as $email_number) {
        
        		$overview = imap_fetch_overview($imap, $email_number, 0)[0];
        		$subject = $overview->subject;
        		$from = $overview->from;
        		$date = $overview->date;
        		$body = imap_fetchbody($imap, $email_number, 1);
        		if (base64_decode($body)) {
            		$body = base64_decode($body);
        		}

        		$email_model->subject = $subject;
        		$email_model->from = $from;
        		$email_model->date = $date;
        		$email_model->body = $body;
        		$email_model->save();

        		echo "Subject: $subject\n";
        		echo "From: $from\n";
        		echo "Date: $date\n";
        		echo "Body: $body\n";
        		echo "-----------------------------------\n";
    		}
		} else {
    		echo "No emails found.\n";
		}	

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
