# AI Quiz Generator from Voice - Gemini CLI Context

This file provides context for the Gemini CLI to understand the project and its history.

## Project Overview

The goal of this project is to create a comprehensive WordPress plugin named "AI Quiz Generator from Voice". This plugin empowers educators to transform audio lectures or voice notes into educational content, including transcripts, summaries, and interactive quizzes.

## What has been done

*   **Initial Scaffolding:** The basic plugin structure was created, including the main plugin file, directories for includes, admin, public, assets, and templates.
*   **Custom Post Types and Taxonomies:** The `lecture` and `quiz` custom post types were created, along with the `course` and `level` custom taxonomies.
*   **Custom Database Tables:** The `wp_quiz_attempts` and `wp_quiz_answers` custom database tables were created to store quiz results.
*   **Custom User Roles:** The `teacher` and `student` custom user roles were created with specific capabilities.
*   **Manager Settings Page:** A settings page was created for the manager to configure the API keys for the STT and LLM services.
*   **Teacher Workflow:**
    *   An upload page was created for teachers to upload audio files, provide a title, and select a course and level.
    *   The audio file is processed in the background using WP-Cron.
    *   The plugin calls the selected STT and LLM services to generate a transcript, summary, and quiz.
    *   A meta box is displayed on the lecture edit screen for the teacher to review and edit the generated content.
*   **Student Workflow:**
    *   A student portal was created using a shortcode to display a list of available quizzes.
    *   A custom template is used to display the quiz, including the transcript, summary, and quiz questions.
    *   Students can take the quiz and their results are saved in the database.
    *   A quiz history section is displayed on the student portal.
*   **AJAX Implementation:**
    *   The quiz submission is handled via AJAX for a smoother user experience.
    *   The teacher's audio file upload is handled via AJAX with background processing.
*   **PDF Export:** A button was added to the quiz page to download a PDF of the transcript and summary.
*   **Results Tracking:** Views were created for both students and teachers to track quiz results.
*   **Email Notifications:** Email notifications are sent to students when a new quiz is published.
*   **Multiple Classes:** The plugin was improved to allow teachers and students to be assigned to multiple courses and levels.
*   **Code Quality:**
    *   The code was refactored to be more object-oriented, with a main plugin class.
    *   Composer was set up for dependency management.
    *   A security audit was performed, and a SQL injection vulnerability was fixed.
    *   Documentation was added to the code.

## What can be done in the future

*   **Implement the actual API calls:** The current implementation has placeholder functions for the API calls. These need to be replaced with actual API calls to the STT and LLM services.
*   **Implement PDF generation:** The current implementation has a placeholder for PDF generation. A library like TCPDF or FPDF should be used to generate the PDF.
*   **Add more question types:** The plugin currently only supports multiple-choice questions. Other question types, such as true/false or fill-in-the-blanks, could be added.
*   **Add more detailed analytics:** More detailed analytics could be provided for teachers and managers.
*   **Add unit tests:** Unit tests should be added to the plugin to ensure that it is working correctly.
*   **Improve the UI/UX:** The user interface and user experience could be further improved.
