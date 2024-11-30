# Project Enrollify
This is a pet project that I've started outside of my regular Technical Support work and it was spurred on by a workflow that a customer had. It involved the following: 

* The backend of a online store where the manager enters in a user's email address and selects a course to enroll them in.
* Next, the customer wanted the learner to be able to login to a learner dashboard within the same 3rd party platform.
* Finally, in the learner dashboard, when the learner selects a course that their manager has enrolled them in the learner is taken into that course enrollment within LearnUpon.

## Current capabilities
 1. Lists all available courses in a LearnUpon portal/instance
 2. Checks if a user exists in a LearnUpon portal/instance
 3. Creates the user, as a learner, if they don't exist in LearnUpon
 4. Enrolls the user in the course that is selected from the dropdown by the manager

## Next Steps
- [x] Build out the navigation to take somebody to the learner's dashboard (Could look at creating a login option at a later date)
- [X] Show all enrollments to the learner (course thumbnail, description, enrollment data -- e.g. date created)
- [X] If the learner selects a course tile they are sent, via SQSSO*, to the LearnUpon portal
- [ ] OPTIONAL: Create a login for the learner
- [ ] OPTIONAL: Implement [LearnUpon Anywhere](https://support.learnupon.com/hc/en-us/articles/18314839521693-LearnUpon-Anywhere-getting-started#Makelearninganintegralpartofyourusers'dailyexperience) - iframe option within the web page itself.
