<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;

class CoursesController extends Controller {
    public function addInterestedMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);
        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();
            $interestedMembers = [];
            if (isset($courseData['interestedMembers'])) {
                $interestedMembers = $courseData['interestedMembers'];
            }
            if (!array_search($request->session()->get('userID'), $interestedMembers)) {
                $interestedMembers[] = $request->session()->get('userID');
            }

            $response = $course->set([
                'interestedMembers' => $interestedMembers
            ], ['merge' => true]);
            if (isset($response['updateTime'])) {
                return 200;
            };
        }

        abort(500, 'error while adding interested member to course');
    }

    public function signupMember(Request $request) {
        $request->validate([
            'course_id' => 'required|string|max:255',
        ]);

        $firestore = new FirestoreClient();

        $course = $firestore->collection('courses')->document($request->course_id);
        if ($course->snapshot()->exists()) {
            $courseData = $course->snapshot()->data();
            $interestedMembers = [];
            if (isset($courseData['interestedMembers'])) {
                $interestedMembers = $courseData['interestedMembers'];
            }
            $_array_pos = array_search($request->session()->get('userID'), $interestedMembers);
            if ($_array_pos !== false) {
                unset($interestedMembers[$_array_pos]);
            }

            $permanentMembers = [];
            if (isset($courseData['permanentMembers'])) {
                $permanentMembers = $courseData['permanentMembers'];
            }
            if (!array_search($request->session()->get('userID'), $permanentMembers)) {
                $permanentMembers[] = $request->session()->get('userID');
            }

            $response = $course->set([
                'interestedMembers' => $interestedMembers,
                'permanentMembers'  => $permanentMembers
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
            $interestedMembers = [];
            if (isset($courseData['interestedMembers'])) {
                $interestedMembers = $courseData['interestedMembers'];
            }
            $_array_pos = array_search($request->session()->get('userID'), $interestedMembers);
            if ($_array_pos !== false) {
                unset($interestedMembers[$_array_pos]);
            }

            $response = $course->set([
                'interestedMembers' => $interestedMembers,
            ], ['merge' => true]);
            if (isset($response['updateTime'])) {
                return 200;
            };
        }

        abort(500, 'error while removing interested member from course');
    }
}
