<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;

class CoursesController extends Controller {
    public function addInterestedMember(Request $request) {
        $request->validate([
            'id'        => 'required|string|max:255',
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
            $interestedMembers[] = $request->id;

            $response = $course->set([
                'interestedMembers' => $interestedMembers
            ], ['merge' => true]);
            if (isset($response['updateTime'])) {
                return 200;
            };
        }

        abort(500, 'error while adding interested user to course');
    }
}
