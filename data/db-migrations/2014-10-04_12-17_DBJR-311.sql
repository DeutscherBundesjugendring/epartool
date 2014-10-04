ALTER TABLE `email_template`
    DROP INDEX `email_template_name_idx`,
    ADD UNIQUE `email_template_name_project_code_key` (`name`, `project_code`);
