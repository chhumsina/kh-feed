<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

            $userName = $this->details['username'];
            $body = $this->details['body'];
            $to_name = 'khfeed';
            $to_email = 'chhumsina@gmail.com';
            $data = array('name'=>$userName, 'body'=> $body);
            Mail::send('emails.mail', $data, function($message) use ($to_name, $to_email,$userName) {
            $message->to($to_email, $to_name)
            ->subject('Create Post');
            $message->from('sinachhum.cist@gmail.com',$userName);
            });
    }
}
