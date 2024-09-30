<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once __DIR__.'/../RestController.php';
use CustomerApi\RestController;

class Knowledge_base extends RestController
{
    public function __construct()
    {
        parent::__construct();
        register_language_files('mmb', ["customers_api"]);
        load_client_language();
    }

    /**
     * @api {get} /mmb/v1/knowledge_base List All Knowledge Base
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetAllKnowledgeBase
     *
     * @apiGroup Knowledge Base
     *
     * @apiSampleRequest /mmb/v1/knowledge_base
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Array}   data    Knowledge Base information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *       {
     *           "status": true,
     *           "data": [
     *               {
     *                   "groupid": "1",
     *                   "name": "Group Name 1",
     *                   "group_slug": "group-name-1",
     *                   "description": "",
     *                   "active": "1",
     *                   "color": "#000000",
     *                   "group_order": "1",
     *                   "articles": [
     *                       {
     *                           "slug": "coustom-project",
     *                           "subject": "coustom project ",
     *                           "description": "",
     *                           "active_article": "1",
     *                           "articlegroup": "1",
     *                           "articleid": "1",
     *                           "staff_article": "0",
     *                           "datecreated": "2023-05-15 12:25:14"
     *                       },
     *                       {
     *                           "slug": "coustom-project-2",
     *                           "subject": "coustom project",
     *                           "description": "<p>test</p>",
     *                           "active_article": "1",
     *                           "articlegroup": "1",
     *                           "articleid": "2",
     *                           "staff_article": "0",
     *                           "datecreated": "2023-05-17 18:29:50"
     *                       }
     *                   ]
     *               }
     *           ],
     *           "message": "Articles retrived successfully"
     *       }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message No data found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data found"
     *     }
     */

    /**
     * @api {get} /mmb/v1/knowledge_base/article/:slug Request Knowledge Base By Slug Name
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetKnowledgeBaseBySlug
     *
     * @apiGroup Knowledge Base
     *
     * @apiSampleRequest /mmb/v1/knowledge_base/article/coustom-project-2
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Object}  data    Knowledge Base information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *           "status": true,
     *           "data": {
     *               "slug": "coustom-project-2",
     *               "articleid": "2",
     *               "articlegroup": "1",
     *               "subject": "coustom project",
     *               "description": "<p>test</p>",
     *               "active_article": "1",
     *               "active_group": "1",
     *               "group_name": "Group Name 1",
     *               "staff_article": "0"
     *           },
     *           "message": "Articles retrived successfully"
     *       }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data found"
     *     }
     */

    /**
     * @api {get} /mmb/v1/knowledge_base/group/:slug Request Knowledge Base By Group Slug Name
     *
     * @apiVersion 1.0.0
     *
     * @apiName GetKnowledgeBaseByGroupSlug
     *
     * @apiGroup Knowledge Base
     *
     * @apiSampleRequest /mmb/v1/knowledge_base/group/group-2
     *
     * @apiSuccess {Boolean} status  Response status.
     * @apiSuccess {Object}  data    Knowledge Base information.
     * @apiSuccess {String}  message Success message.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *       {
     *       "status": true,
     *       "data": [
     *           {
     *               "groupid": "2",
     *               "name": "Group 2",
     *               "group_slug": "group-2",
     *               "description": "test",
     *               "active": "1",
     *               "color": "#000000",
     *               "group_order": "2",
     *               "articles": [
     *                   {
     *                       "slug": "php-development",
     *                       "subject": "php development",
     *                       "description": "<p>ftrgh</p>",
     *                       "active_article": "1",
     *                       "articlegroup": "2",
     *                       "articleid": "3",
     *                       "staff_article": "0",
     *                       "datecreated": "2023-05-18 10:23:26"
     *                   },
     *                   {
     *                       "slug": "test-admin-subject",
     *                       "subject": "test admin subject",
     *                       "description": "<p>r5yggre</p>",
     *                       "active_article": "1",
     *                       "articlegroup": "2",
     *                       "articleid": "4",
     *                       "staff_article": "0",
     *                       "datecreated": "2023-05-18 10:23:39"
     *                   }
     *               ]
     *           }
     *       ],
     *       "message": "Articles retrived successfully"
     *   }
     *
     * @apiError {Boolean} status  Response status.
     * @apiError {String}  message Error message.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data found"
     *     }
     */
    public function knowledge_base_get()
    {
        $group   = $this->get('group');
        $article = $this->get('article');

        if (empty($group) && empty($article)) {
            $articleData = get_all_knowledge_base_articles_grouped(true);

            $this->response([
                'data'    => !empty($articleData) ? $articleData : null,
                'message' => !empty($articleData) ? _l('data_retrived_success') : _l('data_not_found'),
            ], 200);
        }

        if (!empty($article)) {
            $this->load->model('knowledge_base_model');

            $articleData = $this->knowledge_base_model->get(false, $article);

            $this->response([
                'data'    => !empty($articleData) ? $articleData : null,
                'message' => !empty($articleData) ? _l('data_retrived_success') : _l('data_not_found'),
            ], 200);
        }

        if (!empty($group)) {
            $this->load->model('knowledge_base_model');

            $where_kb    = 'articlegroup IN (SELECT groupid FROM '.db_prefix().'knowledge_base_groups WHERE group_slug="'.$group.'")';
            $articleData = get_all_knowledge_base_articles_grouped(true, $where_kb);

            $this->response([
                'data'    => !empty($articleData) ? $articleData : null,
                'message' => !empty($articleData) ? _l('data_retrived_success') : _l('data_not_found'),
            ], 200);
        }
    }
}

/* End of file Knowledge_base.php */
