<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\DomCrawler\Crawler;

class FetchWebsiteContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $lead;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $lead)
    {
        $this->url = $url;
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Fetch the website content using Guzzle
        $client = new Client();
        $response = $client->request('GET', $this->url);
        $html = $response->getBody()->getContents();

        // Create a Crawler instance from the HTML
        $crawler = new Crawler($html);

        // Extract the meta description
        $metaDescription = $crawler->filter('meta[name="description"]')->attr('content');

        // Save the meta description to the lead
        if (!empty($metaDescription)) {
            $this->lead->website_content = $metaDescription;
        } else {
            $this->lead->website_content = "n/a";
        }

        $this->lead->save();
    }
}
