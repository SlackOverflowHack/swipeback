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

'id'        => 'required|string|max:255',
'email', 
'password', 
'firstname', 
'lastname', 
'birthDate'
```

### course
```
POST /api/course/addInterestedMember

'id'        => 'required|string|max:255',
'course_id' => 'required|string|max:255',
```
