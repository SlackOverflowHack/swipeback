<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;

class CoursesController extends Controller {
    public function add(Request $request) {
        $requirements = [
            'titel'                   => 'required|string|max:255',
            'beschreibung'            => 'required|string|max:255',
            'schlagwoerter'           => 'required|array',
            'kontakt'                 => 'required|array',
            'ort'                     => 'required|array',
            'termine'                 => 'required|array',
            'zielgruppe'              => 'required|array',
            'maxanzahl'               => 'required|int',
            'intern'                  => 'required|boolean',
            'wochentag'               => 'array',
            'interestedMembers'       => 'array',
            'uninterestedMembers'     => 'array',
            'permanentMembers'        => 'array'
        ];

        $request->validate($requirements);
        $data = $request->only(array_keys($requirements));

        if(!isset($data['interestedMembers']))      $data['interestedMembers'] = [];
        if(!isset($data['uninterestedMembers']))    $data['uninterestedMembers'] = [];
        if(!isset($data['permanentMembers']))       $data['permanentMembers'] = [];

        $firestore = new FirestoreClient();
        $response = $firestore->collection('courses')->newDocument()->set($data);

        if (isset($response['updateTime'])) return 200;

        abort(500, 'error while adding course');
    }

    public function addInterestedMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);
        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();

            $interestedMembers = $this->addToArrayIfNeeded($request->session()->get('userID'), $courseData['interestedMembers']);
            $uninterestedMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['uninterestedMembers']);
            $permanentMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['permanentMembers']);

            $response = $course->set([
                'interestedMembers'   => $interestedMembers,
                'uninterestedMembers' => $uninterestedMembers,
                'permanentMembers'    => $permanentMembers
            ], ['merge' => true]);
            if (isset($response['updateTime'])) {
                return 200;
            };
        }

        abort(500, 'error while adding interested member to course');
    }

    public function addUninterestedMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);
        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();

            $interestedMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['interestedMembers']);
            $uninterestedMembers = $this->addToArrayIfNeeded($request->session()->get('userID'), $courseData['uninterestedMembers']);
            $permanentMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['permanentMembers']);

            $response = $course->set([
                'interestedMembers'   => $interestedMembers,
                'uninterestedMembers' => $uninterestedMembers,
                'permanentMembers'    => $permanentMembers
            ], ['merge' => true]);
            if (isset($response['updateTime'])) {
                return 200;
            };
        }

        abort(500, 'error while adding uninterested member to course');
    }

    public function addPermanentMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);
        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();

            $interestedMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['interestedMembers']);
            $uninterestedMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['uninterestedMembers']);
            $permanentMembers = $this->addToArrayIfNeeded($request->session()->get('userID'), $courseData['permanentMembers']);

            $response = $course->set([
                'interestedMembers'   => $interestedMembers,
                'uninterestedMembers' => $uninterestedMembers,
                'permanentMembers'    => $permanentMembers
            ], ['merge' => true]);
            if (isset($response['updateTime'])) {
                return 200;
            };
        }

        abort(500, 'error while adding permanent member to course');
    }

    public function addAppointment(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
            'date' => 'required'
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);

        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();

            $termine = [];
            if (isset($courseData['termine'])) {
                $termine = $courseData['termine'];
            }
            foreach($termine as $termin) {
                if($termin['datum'] == $request->date) {
                    abort(500, 'appointment with this date already exists');
                }
            }
            $termine[] = [
                'anmeldungen' => [],
                'abmeldungen' => [],
                'datum' => $request->date
            ];

            $response = $course->set([
                'termine'   => $termine,
            ], ['merge' => true]);

            if (isset($response['updateTime'])) {
                return 200;
            };
        }

        abort(500, 'error while adding appointment to course');
    }

    /**
     * add a member for a single appointment inside a course
     */
    public function addAppointmentMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
            'date' => 'required'
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);

        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();

            for($i = 0; $i < sizeof($courseData['termine']); $i++) {
                if ($courseData['termine'][$i]['datum'] == $request->date) {
                    $anmeldung = $this->addToArrayIfNeeded($request->session()->get('userID'), $courseData['termine'][$i]['anmeldungen']);
                    $courseData['termine'][$i]['anmeldungen'] = $anmeldung;
                    
                    $response = $course->set([
                        'termine'    => $courseData['termine']
                    ], ['merge' => true]);

                    if (isset($response['updateTime'])) {
                        return 200;
                    };
                }
            }
        }

        abort(500, 'error while adding appointment member to course');
    }

    public function removeAppointmentMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
            'date' => 'required'
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);

        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();

            for($i = 0; $i < sizeof($courseData['termine']); $i++) {
                if ($courseData['termine'][$i]['datum'] == $request->date) {
                    $anmeldung = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['termine'][$i]['anmeldungen']);
                    $courseData['termine'][$i]['anmeldungen'] = $anmeldung;
                    
                    $response = $course->set([
                        'termine'    => $courseData['termine']
                    ], ['merge' => true]);

                    if (isset($response['updateTime'])) {
                        return 200;
                    };
                }
            }
        }

        abort(500, 'error while removing appointment member from course');
    }

    /**
     * unsubscribe a member from a single appointment in a course
     */
    public function addSingleMissingAppointmentMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
            'date' => 'required'
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);

        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();

            $userID = $request->session()->get('userID');

            if(in_array($userID, $courseData['permanentMembers'])) {

                for($i = 0; $i < sizeof($courseData['termine']); $i++) {
                    if ($courseData['termine'][$i]['datum'] == $request->date) {
                        $anmeldung = $this->addToArrayIfNeeded($userID, $courseData['termine'][$i]['abmeldungen']);
                        $courseData['termine'][$i]['abmeldungen'] = $anmeldung;
                        
                        $response = $course->set([
                            'termine'    => $courseData['termine']
                        ], ['merge' => true]);

                        if (isset($response['updateTime'])) {
                            return 200;
                        };
                    }
                }
            }
        }

        abort(500, 'error while adding single missing appointment member to course');
    }

    public function removeMissingSingleAppointmentMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
            'date' => 'required'
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);

        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();

            for($i = 0; $i < sizeof($courseData['termine']); $i++) {
                if ($courseData['termine'][$i]['datum'] == $request->date) {
                    $anmeldung = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['termine'][$i]['abmeldungen']);
                    $courseData['termine'][$i]['abmeldungen'] = $anmeldung;
                    
                    $response = $course->set([
                        'termine'    => $courseData['termine']
                    ], ['merge' => true]);

                    if (isset($response['updateTime'])) {
                        return 200;
                    };
                }
            }
        }

        abort(500, 'error while removing single missing appointment member from course');
    }

    public function removeInterestedMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);
        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();

            $interestedMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['interestedMembers']);
            $uninterestedMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['uninterestedMembers']);
            $permanentMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['permanentMembers']);

            $response = $course->set([
                'interestedMembers'   => $interestedMembers,
                'uninterestedMembers' => $uninterestedMembers,
                'permanentMembers'    => $permanentMembers
            ], ['merge' => true]);
            if (isset($response['updateTime'])) {
                return 200;
            };
        }

        abort(500, 'error while removing interested member from course');
    }

    public function removePermanentMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);
        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();

            $interestedMembers = $this->addToArrayIfNeeded($request->session()->get('userID'), $courseData['interestedMembers']);
            $uninterestedMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['uninterestedMembers']);
            $permanentMembers = $this->removeFromArrayIfPossible($request->session()->get('userID'), $courseData['permanentMembers']);

            $response = $course->set([
                'interestedMembers'   => $interestedMembers,
                'uninterestedMembers' => $uninterestedMembers,
                'permanentMembers'    => $permanentMembers
            ], ['merge' => true]);
            if (isset($response['updateTime'])) {
                return 200;
            };
        }

        abort(500, 'error while removing permanent member from course');
    }

    private function removeFromArrayIfPossible($needle, $source): array {
        $dest = [];
        if (isset($source)) {
            $dest = $source;
        }
        while (in_array($needle, $dest)) {
            unset($dest[array_search($needle, $dest)]);
        }

        return $dest;
    }

    private function addToArrayIfNeeded($needle, $source): array {
        $dest = [];
        if (isset($source)) {
            $dest = $source;
        }
        if (!in_array($needle, $dest)) {
            $dest[] = $needle;
        }

        return $dest;
    }
}
