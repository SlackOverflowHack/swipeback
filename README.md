# SwipeBack

## The backend for swiper

This application handles all write-related transactions to the Firestore Database inside our firebase application.

## Connect from php to Firebase FireStore

https://developers.google.com/identity/protocols/oauth2/service-account#creatinganaccount

## API without authentication

### user

```
POST /api/user/register

'email'    => 'required|string|max:255',
'password' => 'required|string|max:255',
```

## API with authentication

### user

```
POST /api/user/update

'email',        => optional
'password',     => optional
'firstname',    => optional
'lastname',     => optional
'birthDate'     => optional
```

### course

```
POST /api/course/addInterestedMember

'course_id' => 'required|string|max:255',
```

```
POST /api/course/addUninterestedMember

'course_id' => 'required|string|max:255',
```

```
POST /api/course/removeInterestedMember

'course_id' => 'required|string|max:255',
```

```
POST /api/course/addPermanentMember

'course_id' => 'required|string|max:255',
```

```
POST /api/course/removePermanentMember

'course_id' => 'required|string|max:255',
```

```
POST /api/course/add

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
```
