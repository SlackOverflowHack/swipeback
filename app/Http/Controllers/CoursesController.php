<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;

class CoursesController extends Controller {
    public function add(Request $request) {
        $requirements = [
            'number'              => 'required|string|max:255',
            'name'                => 'required|string|max:255',
            'organizer'           => 'required|string|max:255',
            'price'               => 'required|numeric',
            'discountPossible'    => 'required|boolean',
            'place'               => 'required|string|max:255',
            'street'              => 'required|string|max:255',
            'postcode'            => 'required|integer',
            'city'                => 'required|string|max:255',
            'barrierFree'         => 'required|boolean',
            'duration'            => 'required|integer',
            'startingDate'        => 'required|date',
            'endDate'             => 'required|date',
            'frequency'           => 'required|integer',
            'intern'              => 'required|boolean',
            'minNrMembers'        => 'required|integer',
            'maxNrMembers'        => 'required|integer',
            'requirements'        => 'required|array',
            'tags'                => 'required|array',
            'targetGroup'         => 'required|array',
            'interestedMembers'   => 'required|array',
            'uninterestedMembers' => 'required|array',
            'permanentMembers'    => 'required|array',
        ];

        $request->validate($requirements);
        $data = $request->only(array_keys($requirements));

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
