# Coupons Documentation

The `coupons` table in the database has the following fields:

- `name`: The name of the coupon.
- `code`: The unique code associated with the coupon.
- `desc`: A description of the coupon.
- `limit_per_use`: The maximum number of times the coupon can be used per user.
- `discount`: The discount amount or percentage offered by the coupon.
- `eligibility`: The eligibility criteria for the coupon, such as a certain price range, all users, or new users.
- `start_date`: The starting date of the coupon's validity.
- `end_date`: The ending date of the coupon's validity.
- `valid_location`: The location(s) where the coupon is valid.
- `num_coupons`: The number of available coupons.
- `terms_and_conditions`: The terms and conditions associated with the coupon.

The `coupons` table is related to other tables, including:

- `coupons_eligibility`: This table defines the eligibility criteria for the coupon, such as the price range or user type like new or all users.
- `coupons_valid_states`: This table specifies the states where the coupon is valid.

Please refer to the database schema for more details on the relationships between these tables.
