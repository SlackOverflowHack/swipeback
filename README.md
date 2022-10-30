# SwipeBack

## The backend for swiper

This application handles all write-related transactions to the Firestore Database inside our firebase application.

## Deployment
Docker images are built using our private gitlab instance ('git.goebel.app') and uploaded to its internal container registry.
The images are deployed inside our kubernetes cluster [see the kubernetes repository](https://github.com/SlackOverflowHack/k8s-deployment).

## Connect from php to Firebase FireStore

https://developers.google.com/identity/protocols/oauth2/service-account#creatinganaccount

## API without authentication

### user

```
POST /api/user/register

'email'     => 'required|email|max:255',
'password'  => 'required|string|max:255',
'firstname' => 'required|string|max:255',
'lastname'  => 'required|string|max:255',
'birthDate' => 'required'
```

## API with authentication

### user

```
POST /api/user/update

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
```

### appointments

```
POST /api/course/appointments/add

'course_id' => 'required|string|max:255',
'date'      => 'required'
```

```
POST /api/course/appointments/addMember

'course_id' => 'required|string|max:255',
'date'      => 'required'
```

```
POST /api/course/appointments/removeMember

'course_id' => 'required|string|max:255',
'date'      => 'required'
```

```
POST /api/course/appointments/addSingleMissingMember

'course_id' => 'required|string|max:255',
'date'      => 'required'
```

```
POST /api/course/appointments/removeSingleMissingMember

'course_id' => 'required|string|max:255',
'date'      => 'required'
```
