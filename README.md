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

'guid'                    => 'required|string|max:255',
'number'                  => 'required|string|max:255',
'name'                    => 'required|string|max:255',
'untertitel'              => 'required|string|max:255',
'level'                   => 'string|max:255',
'dauer'                   => 'numeric',
'beginn_datum'            => 'required',
'ende_datum'              => '',
'anzahl_termine'          => 'integer',
'intern'                  => 'required|boolean',
'minimale_teilnehmerzahl' => 'integer',
'maximale_teilnehmerzahl' => 'integer',
'schlagwort'              => 'array',
'zielgruppe'              => 'array',
'zertifikat'              => 'array',
'text'                    => 'required|array',
'veranstaltungsort'       => 'required|array',
'termin'                  => 'array',
'preis'                   => 'array',
'dozent'                  => 'array',
'webadresse'              => 'required|array',
'dvv_kategorie'           => 'required|array',
'wochentag'               => 'array',
'interestedMembers'       => 'array',
'uninterestedMembers'     => 'array',
'permanentMembers'        => 'array',
```
