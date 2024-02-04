### Authenticated Route

#### Register User

1. **Method:** POST
2. **URL:** `{{baseUrl}}/api/register`
3. **Body:** Form-data or raw JSON with fields like `name`, `email`, and `password`.
4. **Description:** Adds a new user to the system.
5. **Example Response:**
   ```json
    {
      "token": "4|UlEZ9TXW0BJToVbuJgYFgqv7vc8H7oe91WUcf5i3fd27c5bc"
    }
   ```

#### Login User

1. **Method:** POST
2. **URL:** `{{baseUrl}}/api/login`
3. **Body:** Form-data or raw JSON with `email` and `password`.
4. **Description:** Authenticates a user and returns a token.
5. **Example Response:**
   ```json
    {
      "token": "4|UlEZ9TXW0BJToVbuJgYFgqv7vc8H7oe91WUcf5i3fd27c5bc"
    }
   ```

#### Get Authenticated User

1. **Method:** GET
2. **URL:** `{{baseUrl}}/api/user`
3. **Headers:** Authorization: Bearer {{accessToken}}
4. **Description:** Retrieves the currently authenticated user.

### Users Routes

#### Get All Users

1. **Method:** GET
2. **URL:** `{{baseUrl}}/api/users`
3. **Headers:** Authorization: Bearer {{accessToken}}
4. **Description:** Retrieves all users.

#### Get Specific User

1. **Method:** GET
2. **URL:** `{{baseUrl}}/api/users/:id`
3. **Headers:** Authorization: Bearer {{accessToken}}
4. **Description:** Retrieves information for a specific user.

#### Update User Avatar

1. **Method:** POST
2. **URL:** `{{baseUrl}}/api/users/:id/avatar`
3. **Headers:** Authorization: Bearer {{accessToken}}
4. **Body:** Form-data with the avatar file.
5. **Description:** Updates the avatar for the user.

#### Remove User Avatar

1. **Method:** DELETE
2. **URL:** `{{baseUrl}}/api/users/:id/avatar`
3. **Headers:** Authorization: Bearer {{accessToken}}
4. **Description:** Removes the avatar for the user.

### Non-Authenticated Route

#### Issue Token

1. **Method:** POST
2. **URL:** `{{baseUrl}}/api/oauth/token`
3. **Body:** Form-data or raw JSON with the required OAuth fields.
4. **Description:** Issues a new access token.

### Additional Information

For each endpoint:

- Add **Headers** as needed (for example, `Content-Type: application/json`, `Accept-Language: en`).
- Define **Query Parameters** if any.
- Describe **Path Variables** for routes like `/users/:id`.
