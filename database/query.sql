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