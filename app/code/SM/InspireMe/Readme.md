# SM_InspireMe API Doc

List API for Mobile using:
* Get List Articles:
    * method  : `GET`
    * token   : `anonymous`
    * request : `/V1/blog/getListArticles`
    * params  : `searchCriteria[sortOrders][0][field]=created_at&searchCriteria[sortOrders][0][direction]=desc&searchCriteria[currentPage]=1&searchCriteria[pageSize]=2`
    * response: List Articles, with default sort order by 'created_at - desc'
---
* Get List Articles by Topic (Category) ID:
    * method  : `GET`
    * token   : `anonymous`
    * request : `/V1/blog/getListArticles`
    * params  : `searchCriteria[filter_groups][0][filters][0][field]=category_id&searchCriteria[filter_groups][0][filters][0][value]=1&searchCriteria[filter_groups][0][filters][0][condition_type]=in`
    * response: List Articles belong to selected Topic
--- 
* Get List Articles by Tag ID:
    * method  : `GET`
    * token   : `anonymous`
    * request : `/V1/blog/getListArticles`
    * params  : `searchCriteria[filter_groups][0][filters][1][field]=tag_id&searchCriteria[filter_groups][0][filters][1][value]=1&searchCriteria[filter_groups][0][filters][1][condition_type]=in`
    * response: List Articles belong to selected Tag
--- 
* Search Articles:
    * method  : `GET`
    * token   : `anonymous`
    * request : `/V1/blog/getListArticles`
    * params  : `searchCriteria[filter_groups][0][filters][2][field]=search&searchCriteria[filter_groups][0][filters][2][value]=fun`
    * response: List Articles that match search value
---
* Get Home Articles:
    * method  : `GET`
    * token   : `anonymous`
    * request : `/V1/blog/getHomeArticles`
    * params  : `none`
    * response: List 5 Articles show on homepage
---    
* Get Most Popular:
    * method  : `GET`
    * token   : `anonymous`
    * request : `/V1/blog/getMostPopular`
    * params  : `none`
    * response: List 3 Most Popular Articles
---
* Get Article By ID:
    * method  : `GET`
    * token   : `customer`
    * request : `/V1/blog/post/:postId`
    * params  : `change :postId by ID`
    * response: Article Detail with some special values:
        * is_shop_ingredient: show related products block as +- type
        * mobile_main_content: main content of article
        * mobile_sub_content: if this value isset, article content will show as 2 tabs, fixed tab title as design
        * hot_spot -> pins: 
            * img_h, img_w: pixel - height, width of image that contain pins
            * height, width: pixel - height, width of pin
            * top_percent, left_percent: percent - position of pin on the image
            * custom_text: if this value isset, it will show instead of product
            * position: position to show product when click pin
---
* Add 1 view to Article:
    * method  : `PUT`
    * token   : `admin`
    * request : `/V1/blog/updateViewsCount/:postId`
    * param   : `change :postId by ID`
    * response: true - update success, false - update fail
---
* Get Related Products:
    * method  : `GET`
    * token   : `customer`
    * request : `/V1/blog/getRelatedProducts/:postId`
    * param   : `change :postId by ID`
    * response: List Products that assigned to Article
        * product_value: default value to show if 'related products block' show as type 'shop_by_ingredient'
        * other values : the same as PLP
---
* Add Selected Products To Cart:
    * method  : `POST`
    * token   : `customer`
    * request : `/V1/blog/addSelectedToCart`
    * param   : `send JSON on body (see in postman)`
    * response: 
        * true: add to cart success
        * message: add to cart fail with message
