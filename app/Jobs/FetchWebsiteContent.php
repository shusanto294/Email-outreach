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
        // Fetch the website content
        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
        ];

        $client = new Client(['headers' => $headers]);
        $response = $client->request('GET', $this->url);
        $body = $response->getBody();
        $content = $body->getContents();

        // Load the content into Symfony DomCrawler
        $crawler = new Crawler($content);

        // Specify the tags you want to extract content from (e.g., h1, h2, p, div)
        $tagsToExtract = 'h1, h2, h3, h4, h5, h6, p';

        // Filter the crawler to only include these tags
        $extractedContent = $crawler->filter($tagsToExtract)->each(function (Crawler $node) {
            return $node->text();
        });

        // Join the extracted content into a single string
        $extractedContent = implode(' ', $extractedContent);

        // Clean up the content
        $extractedContent = preg_replace('/\s+/', ' ', $extractedContent); // Replace multiple spaces with a single space
        $extractedContent = preg_replace('/[^A-Za-z0-9\- ]/', '', $extractedContent); // Remove non-alphanumeric characters
        $extractedContent = trim($extractedContent); // Trim leading/trailing spaces

        // Save content to the lead
        if (strlen($extractedContent) > 0) {
            $this->lead->website_content = $extractedContent;
        } else {
            $this->lead->website_content = "n/a";
        }

        $this->lead->save();
    }
}
