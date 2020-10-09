define([
    'jquery',
    'moment'
], function ($, moment) {
    'use strict';
    return {
        push: function (event, data) {
            if (typeof dataLayerSourceObjects !== 'undefined' && dataLayerSourceObjects.customer.loginType !== "null" && data) {
                window.dataLayer = window.dataLayer || [];
                switch (event) {
                    case 'search_inspire_me_failed': {
                        window.dataLayer.push({
                            'event': "search_inspire_me_failed",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID,
                            'query': data['query']
                        });
                    }
                        break;
                    case 'inspire_me_load': {
                        window.dataLayer.push({
                            'event': "inspire_me_load",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'store_name': dataLayerSourceObjects.customer.storeName,
                            'store_ID': dataLayerSourceObjects.customer.storeID
                        });
                    }
                        break;
                    case 'selectTopic_inspire_me': {
                        window.dataLayer.push({
                            'event': "selectTopic_inspire_me",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'articleCategory': data['articleCategory']
                        });
                    }
                        break;
                    case 'sortBy_inspire_me': {
                        window.dataLayer.push({
                            'event': "sortBy_inspire_me",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'sort_by': data['sort_by']
                        });
                    }
                        break;
                    case 'article_engagement': {
                        window.dataLayer.push({
                            'event': "article_engagement",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'content_title': data['content_title'],
                            'content_source': data['content_source'],
                            'content_category': data['content_category'],
                            'content_creator': data['content_creator'],
                            'content_length': data['content_length'],
                            'content_publisheddate': data['content_publisheddate'],
                            'article_tags': data['article_tags'],
                            'article_id': data['article_id']
                        });
                    }
                        break;
                    case 'share_article': {
                        window.dataLayer.push({
                            'event': "share_article",
                            'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                            'userID': dataLayerSourceObjects.customer.userID,
                            'customerID': dataLayerSourceObjects.customer.customerID,
                            'customerType': dataLayerSourceObjects.customer.customerType,
                            'loyalty': dataLayerSourceObjects.customer.loyalty,
                            'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                            'loginType': dataLayerSourceObjects.customer.loginType,
                            'content_title': data['content_title'],
                            'content_source': data['content_source'],
                            'content_category': data['content_category'],
                            'content_creator': data['content_creator'],
                            'content_length': data['content_length'],
                            'content_publisheddate': data['content_publisheddate'],
                            'article_tags': data['article_tags'],
                            'article_id': data['article_id'],
                            'social_media': data['social_media']
                        });
                    }
                        break;
                }
            }
        }
    };
});
