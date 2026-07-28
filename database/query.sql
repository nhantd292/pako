-- cập nhật số lượng tổng đơn mua của khách hàng
UPDATE x_contact c
    INNER JOIN (
    SELECT contact_id, COUNT(id) AS so_don_da_mua
    FROM x_contract
    GROUP BY contact_id
    ) o ON c.id = o.contact_id
    SET c.contract_total = o.so_don_da_mua;
-- lấy số lượng tổng đơn mua để đối chiếu
SELECT o.contact_id, c.name, c.contract_total, COUNT(o.id) AS so_don_da_mua
FROM x_contact c INNER JOIN x_contract o
ON c.id = o.contact_id
GROUP BY c.id

-- cập nhật tổng doanh số của khách hàng
UPDATE x_contact c
    INNER JOIN (
    SELECT contact_id, SUM(o.price_total + o.fee_other) AS doanh_so
    FROM x_contract o
    GROUP BY contact_id
    ) o ON c.id = o.contact_id
    SET c.contract_price_total = o.doanh_so;
-- lấy đối chiếu tổng doanh số của khách hàng
SELECT o.contact_id, c.name, c.contract_price_total, SUM(o.price_total + fee_other) AS doanh_so
FROM x_contact c INNER JOIN x_contract o
ON c.id = o.contact_id
GROUP BY c.id

-- cập nhật dữ liệu trường date trong x_customer_debt
UPDATE x_customer_debt
SET date = LEFT(created, 10)
WHERE created IS NOT NULL AND created != '';


-- Đối chiếu dữ liệu công nợ từ customer_debt và bảng khách hàng
SELECT
    c.id AS contact_id,
    c.amount_owed,
    lcd.new_debt,
    CASE
        WHEN c.amount_owed = lcd.new_debt THEN 'Khớp'
        ELSE 'Lệch'
        END AS check_status
FROM x_contact c
         LEFT JOIN (
    -- Lấy ra giá trị new_debt của bản ghi có id lớn nhất theo từng khách hàng
    SELECT cd1.customer_id, cd1.new_debt
    FROM x_customer_debt cd1
             INNER JOIN (
        SELECT customer_id, MAX(id) as max_id
        FROM x_customer_debt
        GROUP BY customer_id
    ) cd2 ON cd1.id = cd2.max_id
) lcd ON c.id = lcd.customer_id
WHERE (c.amount_owed != lcd.new_debt OR c.amount_owed IS NULL)
  AND lcd.new_debt IS NOT NULL;