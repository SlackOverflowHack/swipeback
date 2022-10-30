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
            if (!array_search($request->id, $interestedMembers)) {
                $interestedMembers[] = $request->id;
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
            unset($interestedMembers[array_search($request->id, $interestedMembers)]);

            $permanentMembers = [];
            if (isset($courseData['permanentMembers'])) {
                $permanentMembers = $courseData['permanentMembers'];
            }
            if (!array_search($request->id, $permanentMembers)) {
                $permanentMembers[] = $request->id;
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
}
