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
POST /api/course/removeInterestedMember

'course_id' => 'required|string|max:255',
```

```
POST /api/course/signupMember

'course_id' => 'required|string|max:255',
```
