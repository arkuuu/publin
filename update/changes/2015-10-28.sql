-- Change masterthesis to mastersthesis according to bibtex (Issue #19)
UPDATE `types`
SET `name` = 'mastersthesis'
WHERE `id` = '6';
