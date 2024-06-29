# Project Enrollify
This pet project that I've started outside of my regular Technical Support work was spurred on by a workflow that a customer had. It involved the following: 

* Backend of a shop front where the manager enters in a user's email address and selects a course to enroll them in.
* Afterwards they want the user to be able to login to a Dashboard within the same 3rd party platform.
* When the learner selects a course that they have been enrolled in in their dashboard they are taken into that course enrollment within LearnUpon.

## Current capabilities
 This app currently can:
 1. Checks if a user exists in a portal
 2. If they don't it creates the user
 3. Enrolls the user in the course selected from the dropdown

## Next Steps
1. Build out the navigation to take somebody to the learner's dashboard (Could look at creating a login option at a later point)
2. Show all enrollments to the learner (course thumbnail, description, enrollment data -- e.g. date created)
3. If the learner selects a course tile they are sent, via SQSSO, to the LearnUpon portal


