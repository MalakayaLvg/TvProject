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
        $filmsAPIDATA = $response->toArray();
        $films = [];
        foreach ($filmsAPIDATA['results'] as $result){
            $responseFilm = $this->clientInterface->withOptions([
                'base_uri' => "https://api.themoviedb.org/3/movie/{$result['id']}",
                'headers' => [
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI2NTc4ZGRlZjgzNTk4ZmNkNjJiNjdiODY5YWVjZjU1NyIsIm5iZiI6MTcyODQ2MDMxMi41MDYyMjYsInN1YiI6IjY3MDYzM2ZjYTg4NjE0ZDZiMDhhZDcyYiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.Co3wn3Bt2cx63hnOz3SCfXYYL4BXCO3VevKV6W3Et2E',
                ]
            ])->request('GET', '');
            $filmAPITDATA= $responseFilm->toArray();
             $films[]=[
                 "title"=>$filmAPITDATA['title'],
                 "runtime"=>$filmAPITDATA['runtime'],
                 "description"=>$filmAPITDATA['overview'],
                 "publish_date"=>$filmAPITDATA['release_date'],
                 "critical_rate"=>$filmAPITDATA['vote_average']*5/10,
             ];
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
                    ];

                }
                //get data of one season
                $season = [
                    "episodes" => $episodes,
                    "title" => $seasonAPIDATA['name'],
                    "description" => $seasonAPIDATA['overview'],
                    "publish_date" => new DateTime($seasonAPIDATA['air_date']),

                    "number" => $seasonAPIDATA['season_number']];

                //add season in the series
                $series['seasons'][] = $season;

            }

            $allSeries[] = $series;

        }
        return $allSeries;
    }
}