<?php

namespace App\Service;

use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestService
{
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->clientInterface = $httpClient;
    }

    public function getFilms(): array
    {
        $response = $this->clientInterface->withOptions([
            'base_uri' => 'https://api.themoviedb.org/3/discover/movie',
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI2NTc4ZGRlZjgzNTk4ZmNkNjJiNjdiODY5YWVjZjU1NyIsIm5iZiI6MTcyODQ2MDMxMi41MDYyMjYsInN1YiI6IjY3MDYzM2ZjYTg4NjE0ZDZiMDhhZDcyYiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.Co3wn3Bt2cx63hnOz3SCfXYYL4BXCO3VevKV6W3Et2E',
            ]
        ])->request('GET', '');
        $content = $response->toArray();
        $films = $content['results'];
        for ($k = 0; $k < count($films); $k++) {
            unset($films[$k]['adult']);
            unset($films[$k]['backdrop_path']);
            unset($films[$k]['id']);
            unset($films[$k]['original_title']);
            unset($films[$k]['popularity']);
            unset($films[$k]['vote_count']);
            unset($films[$k]['video']);

            $description = $films[$k]['overview'];
            unset($films[$k]['overview']);
            $films[$k]['description'] = $description;

            $date = $films[$k]['release_date'];
            unset($films[$k]['release_date']);
            $films[$k]['publish_date'] = new DateTime($date);

            $rate = $films[$k]['vote_average'];
            unset($films[$k]['vote_average']);
            $films[$k]['critical_rate'] = $rate * 5 / 10;
            $films[$k]['seen'] = false;
        }
        return $films;
    }

    public function getSeries(): array
    {
        $response = $this->clientInterface->withOptions([
            'base_uri' => 'https://api.themoviedb.org/3/discover/tv',
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI2NTc4ZGRlZjgzNTk4ZmNkNjJiNjdiODY5YWVjZjU1NyIsIm5iZiI6MTcyODQ2MDMxMi41MDYyMjYsInN1YiI6IjY3MDYzM2ZjYTg4NjE0ZDZiMDhhZDcyYiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.Co3wn3Bt2cx63hnOz3SCfXYYL4BXCO3VevKV6W3Et2E',
            ]
        ])->request('GET', '');
        $content = $response->toArray();
        $allSeries = [];
        foreach ($content['results'] as $series) {
            $responseTvSeries = $this->clientInterface->withOptions([
                'base_uri' => "https://api.themoviedb.org/3/tv/{$series['id']}",
                'headers' => [
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI2NTc4ZGRlZjgzNTk4ZmNkNjJiNjdiODY5YWVjZjU1NyIsIm5iZiI6MTcyODQ2MDMxMi41MDYyMjYsInN1YiI6IjY3MDYzM2ZjYTg4NjE0ZDZiMDhhZDcyYiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.Co3wn3Bt2cx63hnOz3SCfXYYL4BXCO3VevKV6W3Et2E',
                ]
            ])->request('GET', '');
            $serieAPIDATA = $responseTvSeries->toArray();

            //get data of  oneseries
            $series = [
                'runtime' => $serieAPIDATA["episode_run_time"],
                "publish_date" =>new DateTime( $serieAPIDATA["first_air_date"]),
                "language" => $serieAPIDATA["languages"],
                "title" => $serieAPIDATA["name"],
                "description" => $serieAPIDATA["overview"],
                "seasons" => [],
                "critical_rate" => $serieAPIDATA["vote_average"] * 5 / 10,
                "seen" => false,
            ];

            //get detail of season
            foreach ($serieAPIDATA['seasons'] as $season) {
                $responseTvSeriesSeasonEpisode = $this->clientInterface->withOptions([
                    'base_uri' => "https://api.themoviedb.org/3/tv/{$serieAPIDATA['id']}/season/{$season['season_number']}",
                    'headers' => [
                        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI2NTc4ZGRlZjgzNTk4ZmNkNjJiNjdiODY5YWVjZjU1NyIsIm5iZiI6MTcyODQ2MDMxMi41MDYyMjYsInN1YiI6IjY3MDYzM2ZjYTg4NjE0ZDZiMDhhZDcyYiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.Co3wn3Bt2cx63hnOz3SCfXYYL4BXCO3VevKV6W3Et2E',
                    ]
                ])->request('GET', '');
                $seasonAPIDATA = $responseTvSeriesSeasonEpisode->toArray();

                $episodes = [];
                //Get content of all episodes
                foreach ($seasonAPIDATA['episodes'] as $episode) {
                    $episodes[] = [
                        "number" => $episode['episode_number'],
                        "title" => $episode['name'],
                        "publish_date" => new DateTime($episode['air_date']),
                        "description" => $episode['overview'],
                        "seen" => false,
                    ];

                }
                //get data of one season
                $season = [
                    "episodes" => $episodes,
                    "name" => $seasonAPIDATA['name'],
                    "description" => $seasonAPIDATA['overview'],
                    "publish_date" => new DateTime($seasonAPIDATA['air_date']),
                    "seen" => false,
                    "number" => $seasonAPIDATA['season_number']];

                //add season in the series
                $series['seasons'][] = $season;

            }

            $allSeries[] = $series;

        }
        return $allSeries;
    }
}