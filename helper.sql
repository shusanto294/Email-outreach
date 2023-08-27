-- Insert new lead
INSERT INTO `leads` (`id`, `name`, `linkedin_profile`, `title`, `company`, `company_website`, `location`, `email`, `campaign_id`, `sent`, `opened`, `technology`, `personalized_line`, `subscribe`, `created_at`, `updated_at`) VALUES (NULL, 'Tonmoy Sarkar', '#', 'CEO', 'Apple', 'example.com', 'USA', 'shusanto294@gmail.com', '12', '0', '0', NULL, NULL, '1', NULL, NULL);

-- Flush all existing leads
UPDATE `leads` SET `email`='shusanto294@gmail.com', `sent`='0', `opened`='0'