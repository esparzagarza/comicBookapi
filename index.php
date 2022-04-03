<?php
/*
Example:
Input: {0:[1, 2], 1: [0], 2: [0], 3: []}
Dont get confused, the above superhero IDs dictionary is equivalent to:
{"Superman":["Batman", "Aquaman"], "Batman": ["Superman"], "Aquaman": ["Superman"], "Wonderwoman": []}
Output: True.
One combination of organizing them is: Batman and Aquaman on day 1 and Superman on day 2. Wonderwoman has no conflicts so she could go any day
is_possible({0:[1, 2], 1:[0], 2:[0], 3:[]}) = True
*/

/*
Testing True
{"Superman":["Batman","Aquaman"],"Batman":["Superman"],"Aquaman":["Superman"],"Wonderwoman":[]}
{"Flash":["Batman","Aquaman"],"Aquaman":["Superman","Flash"],"SuperGirl":["Catwoman"],"Catwoman":["Superman","Flash"],"IronMan":[],"Wonderwoman":[],"Superman":["Batman","Aquaman","Wonderwoman","IronMan"],"Batman":["Superman","Flash"]}

Testing False
{"Flash":["Batman","Aquaman"],"Aquaman":["Superman","Flash"],"SuperGirl":["Catwoman"],"Catwoman":["Superman","Flash"],"IronMan":[],"Wonderwoman":[],"Superman":["Batman","Aquaman","Wonderwoman","IronMan"],"Batman":["Superman","Flash","Aquaman"]}
*/

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

$jsondata = file_get_contents("php://input");
$request = json_decode($jsondata, TRUE);


class ComicBook
{
    public $event = [
        'one' => [],
        'two' => [],
        'msg' => [],
        'result' => true,
    ];

    public $request = array();

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function assignDay(string $day, string $key)
    {
        array_push($this->event[$day], $key);
    }

    public function assignHates(array $hates, string $day)
    {
        foreach ($hates as $key) {
            if (!in_array($key, $this->event[$day])) {
                $this->assignDay($day, $key);
            }
        }
    }

    public function go()
    {
        foreach ($this->request as $heroe => $hates) {
            if (!in_array($heroe, $this->event['one']) && !in_array($heroe, $this->event['two'])) $this->assignDay('one', $heroe);
            if (in_array($heroe, $this->event['one']) && count($hates) > 0) $this->assignHates($hates, 'two');
            if (in_array($heroe, $this->event['two']) && count($hates) > 0) $this->assignHates($hates, 'one');
        }

        $this->verify();
    }

    public function getHatedDay(string $heroe): string
    {
        return in_array($heroe, $this->event['one']) ? 'two' : 'one';
    }

    public function verify()
    {
        foreach ($this->request as $heroe => $hates) {

            $day = $this->getHatedDay($heroe);

            if (count($hates) > 0) {
                foreach ($hates as $key) {
                    if (!in_array($key, $this->event[$day])) {
                        $this->event['result'] = false;
                        array_push($this->event['msg'], "$heroe doesn't match");
                    }
                }
            }
        }

        if ($this->event['result']) array_push($this->event['msg'], "Full Combination Matched");


        $this->response();
    }

    public function response()
    {
        echo json_encode($this->event);
    }
}

$obj = new ComicBook($request);
$obj->go();
