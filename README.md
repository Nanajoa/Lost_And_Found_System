# Ayera - Lost and Found Web Application

Ayera is a full-stack web application developed by students of Ashesi University as part of the CS 415 Software Engineering course. It addresses the inefficiencies of the university's manual lost and found process by providing a digital platform for reporting, viewing, and managing lost and found items.

## Project Team

- Yenma Abambire Bawa – Frontend Developer and Scrum Facilitator  
- Kelsey Goli – UI/UX Designer  
- Victor Nene Kwablah Obahii Ako-Aduonvo – Frontend Developer  
- Papa Yaw Nantwi Badu – Backend Developer  
- Kobina Kyereboah-Coleman – Backend Developer  

## Technologies Used

- Frontend: HTML, CSS, JavaScript  
- Backend: PHP  
- Database: MySQL  
- Project Management: Jira  
- Version Control: GitHub  

## Features

### User Features

- Users can log in using their email address and student ID.
- Users can browse a list of found and missing items.
- Users can filter the list based on when the item was reported (within 24 hours, 7 days, or 30 days).
- Users can report items as missing or found by filling out a form.
- Users can claim items by contacting the finder through the platform.
- Users can mark items as returned.

### Admin Features

- Admins can manage user accounts.
- Admins can delete inappropriate or invalid item reports.
- Admins can view all submitted reports and monitor platform activity.

## Design Patterns Used

- Singleton Pattern for maintaining a single database connection.
- Factory Pattern for creating user and item objects dynamically.
- Observer Pattern for notifying users when there are updates to items they reported or claimed.

## Development Methodology

The project was developed using Agile methodology. The team worked in two-week sprints and used Jira for backlog and sprint management. GitHub was used for version control. Sprint planning meetings, stand-ups, reviews, and retrospectives were held regularly to ensure transparency and continuous improvement.
