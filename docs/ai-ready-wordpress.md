# AI-Ready WordPress: MCP and the Abilities API

In late 2025 and early 2026, WordPress introduced two complementary systems that allow AI agents to interact with WordPress not just as a reader, but as an operator: the **Abilities API** and the **MCP Adapter**.

**Sources:**  

- [Introducing the WordPress Abilities API](https://developer.wordpress.org/news/2025/11/introducing-the-wordpress-abilities-api/) — November 2025
- [From Abilities to AI Agents: Introducing the WordPress MCP Adapter](https://developer.wordpress.org/news/2026/02/from-abilities-to-ai-agents-introducing-the-wordpress-mcp-adapter/) — February 2026

---

## 1. The WordPress Abilities API

Announced November 2025, shipping as a Composer package with planned WordPress core integration in 6.9.

The Abilities API is a **central registry** where plugins and themes declare their functions in a machine-readable format. It is described as "a one-stop shop for what WordPress or any plugin/theme can do, registered in a way everyone (and everything) can understand."

### Key Characteristics

- **Discoverable** — abilities can be listed and inspected via standard interfaces
- **Interoperable** — uniform JSON Schema allows unrelated components to compose workflows
- **Security-first** — explicit `permission_callback` controls who can invoke each ability
- **Gradual adoption** — initially a Composer package; first implementations land in WordPress 6.9

### Registering an Ability

Use `wp_register_ability()` on the `wp_abilities_api_init` hook:

```php
add_action( 'wp_abilities_api_init', 'my_plugin_register_sentiment_ability' );

function my_plugin_register_sentiment_ability() {
    wp_register_ability( 'my-plugin/analyze-sentiment', [
        'label'       => __( 'Analyze Post Sentiment', 'my-plugin' ),
        'description' => __( 'Evaluates the emotional tone of a specific post.', 'my-plugin' ),
        'category'    => 'site-management',

        'input_schema' => [
            'type'       => 'object',
            'properties' => [
                'post_id' => [
                    'type'        => 'integer',
                    'description' => 'The ID of the post to analyze.',
                ],
            ],
            'required' => [ 'post_id' ],
        ],

        'output_schema' => [
            'type'       => 'object',
            'properties' => [
                'sentiment' => [ 'type' => 'string' ],
                'score'     => [ 'type' => 'number' ],
            ],
        ],

        'execute_callback'    => 'my_plugin_do_sentiment_analysis',
        'permission_callback' => function() {
            return current_user_can( 'edit_posts' );
        },

        // Required to expose this ability via MCP:
        'meta' => [
            'mcp' => [ 'public' => true ],
        ],
    ] );
}

function my_plugin_do_sentiment_analysis( $args ) {
    $post_id = $args['post_id'];
    $content = get_post_field( 'post_content', $post_id );

    // Your analysis logic here.
    return [
        'sentiment' => 'positive',
        'score'     => 0.85,
    ];
}
```

### Naming Rules

- Always use `prefix/action-name` format (e.g., `my-plugin/analyze-sentiment`)
- Lowercase only — dashes, not underscores or spaces
- JSON Schema is **mandatory** — the AI needs it to know how to supply and interpret data

### MCP Opt-In Flag

Abilities are **not exposed to MCP by default**. Each ability must explicitly set:

```php
'meta' => [
    'mcp' => [ 'public' => true ],
],
```

To add this flag to a core ability (without modifying core), use the `wp_register_ability_args` filter.

---

## 2. Core Abilities in WordPress 6.9

WordPress 6.9 ships with three built-in abilities:

| Ability | Description |
|---|---|
| `core/get-site-info` | Returns configured site information (all fields or a filtered subset) |
| `core/get-user-info` | Returns authenticated user profile details for personalization and auditing |
| `core/get-environment-info` | Returns runtime context: environment type, PHP version, database info, WordPress version |

!!! warning "Gemini hallucination"
    An earlier version of this doc (sourced from a Gemini conversation) listed `core/list-posts`, `core/update-post`, `core/get-post-content`, `core/search-media`, and `core/check-site-health` as core abilities. **These are not confirmed.** The official announcement lists only the three above.

---

## 3. The MCP Adapter Plugin

The MCP Adapter is a **separate plugin** (not built into core), available from the [GitHub releases page](https://github.com/WordPress/mcp-adapter/releases). Once activated, it:

- Registers a default MCP server (`mcp-adapter-default-server`)
- Converts registered Abilities into MCP primitives
- Exposes three meta-abilities for discovery and execution:

| Meta-Ability | Description |
|---|---|
| `mcp-adapter-discover-abilities` | Lists all available abilities |
| `mcp-adapter-get-ability-info` | Retrieves metadata for a specific ability |
| `mcp-adapter-execute-ability` | Executes a registered ability |

### MCP Primitives

The adapter exposes abilities as one of three MCP primitive types:

- **Tools** — executable functions for actions (most abilities)
- **Resources** — read-only data sources for context (static data)
- **Prompts** — pre-configured templates for specific workflows

Plugins can also register **custom MCP servers** beyond the default, allowing tailored ability exposure per plugin or use case.

---

## 4. Connecting an AI Client

The adapter supports two transport methods depending on your environment.

### STDIO Transport (Local Development)

Requires WP-CLI installed locally. Uses the `wp mcp-adapter serve` command with `--path`, `--server`, and `--user` flags.

### HTTP Transport (Remote / Production)

Uses the [`@automattic/mcp-wordpress-remote`](https://www.npmjs.com/package/@automattic/mcp-wordpress-remote) npm package. Requires Node.js.

**Configure `claude_desktop_config.json`:**  

- Mac: `~/Library/Application Support/Claude/claude_desktop_config.json`
- Windows: `%APPDATA%\Claude\claude_desktop_config.json`

```json
{
  "mcpServers": {
    "my-wordpress-site": {
      "command": "npx",
      "args": ["-y", "@automattic/mcp-wordpress-remote@latest"],
      "env": {
        "WP_API_URL": "https://your-site.local/wp-json/mcp/mcp-adapter-default-server",
        "WP_API_USERNAME": "your_admin_username",
        "WP_API_PASSWORD": "your_application_password_here"
      }
    }
  }
}
```

Authenticates via WordPress **Application Passwords** or custom OAuth.

**Supported AI clients:** Claude Desktop, Claude Code, Cursor, VS Code.  

**Test the connection:** Restart the client, then ask: *"What abilities do you have on my WordPress site?"* The client will call `mcp-adapter-discover-abilities` and list everything registered.  

---

## 5. Model Context Protocol (MCP)

MCP is the underlying open standard (contributed to the **Linux Foundation** in March 2026) that the adapter builds on. It creates a universal interface between AI models and data sources — a "USB-C port" for AI.

Any MCP-compliant model (Claude, OpenAI, Gemini) can use any MCP-compliant server without custom integration code.

### Architecture

- **Host** — the AI application (Claude Desktop, Cursor, etc.)
- **Client** — the component inside the host managing the connection
- **Server** — a service exposing tools/resources (e.g., the WordPress MCP Adapter)

---

## 6. Security Considerations

!!! warning "Harden before exposing Abilities"

    - Only set `meta.mcp.public => true` on abilities you explicitly intend AI clients to access
    - Define `permission_callbacks` strictly — never use `__return_true` for write operations
    - Generate Application Passwords scoped to minimum required permissions
    - Review which abilities third-party plugins register before connecting an AI client
    - Consider STDIO transport for local/staging; HTTP transport for production with OAuth

### How Zsoogi Clipper gates ability access

An AI agent is not a separate principal. Every ability runs inside WordPress as an
authenticated user, and the agent's reach is exactly that user's reach — so the
credential an agent holds *is* the security boundary. An Application Password
belonging to an administrator grants administrator reach.

Enforcement happens at two levels:

1. **Invocation** — every ability declares `permission_callback => current_user_can( 'edit_posts' )`,
   so Contributor is the floor for calling anything at all.

2. **Per clip** — `zsoogi/get-clip` and `zsoogi/get-transcript` call `can_read_clip()`;
   `zsoogi/search-clips` and `zsoogi/export-clips` constrain the query through
   `apply_access_control()` so excluded clips never enter the result set.

`can_read_clip()` allows a read when any of the following holds: the user can
`edit_post` that clip, the user meets the site-wide minimum access capability, or
the user's ID appears in that clip's `_zsoogi_shared_users` list.

`zsoogi/export-clips` deserves the most care. A single call can pull a filtered set
of clips as Markdown or JSON, which makes it the highest-leverage ability in the set.

### What capability checks cannot cover

Capability checks gate **retrieval**. They cannot govern what happens to the text
afterwards.

When an agent calls `zsoogi/get-transcript`, WordPress correctly decides *this user
may read this clip* — and then returns the content to the agent, which sends it on to
its model provider. At that point the text has left your site and is subject to that
provider's data handling and retention.

This is inherent to exposing content over MCP, not a defect in the implementation.
But for a plugin whose premise is that clips stay private, it is a disclosure
obligation rather than a footnote: say plainly that abilities respect WordPress
access rules, *and* that content sent to an agent leaves the site.

---

## 7. Relevance to Zsoogi Clipper

Zsoogi Clipper Pro registers seven abilities, gated behind `License::has_feature( 'abilities' )`:

| Ability | Purpose |
|---|---|
| `zsoogi/create-clip` | Create a clip from structured data (URL, title, excerpt) |
| `zsoogi/get-clip` | Retrieve a single clip |
| `zsoogi/update-clip` | Update title, content (replace or append), or tags |
| `zsoogi/search-clips` | Search by keyword, tag, or source domain |
| `zsoogi/export-clips` | Export a filtered set to Markdown or JSON |
| `zsoogi/get-transcript` | Retrieve a stored YouTube transcript |
| `zsoogi/sideload-clip-images` | Pull external `<img>` sources into the Media Library |

`zsoogi/get-transcript` additionally requires `License::has_feature( 'transcripts' )`.

Together these let an AI assistant search and manage a clip library in natural
language — the defining premium differentiator. See
[How Zsoogi Clipper gates ability access](#how-zsoogi-clipper-gates-ability-access)
for the enforcement model and its limits.
