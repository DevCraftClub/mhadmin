# Cycle ORM Entity Behavior UUID 
[![Latest Stable Version](https://poser.pugx.org/cycle/entity-behavior-uuid/version)](https://packagist.org/packages/cycle/entity-behavior-uuid)
[![Build Status](https://github.com/cycle/entity-behavior-uuid/workflows/build/badge.svg)](https://github.com/cycle/entity-behavior-uuid/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cycle/entity-behavior-uuid/badges/quality-score.png?b=1.x)](https://scrutinizer-ci.com/g/cycle/entity-behavior-uuid/?branch=1.x)
[![Codecov](https://codecov.io/gh/cycle/entity-behavior-uuid/graph/badge.svg)](https://codecov.io/gh/cycle/entity-behavior)
<a href="https://discord.gg/TFeEmCs"><img src="https://img.shields.io/badge/discord-chat-magenta.svg"></a>

The package provides an ability to use `ramsey/uuid` as a Cycle ORM entity column type.

## Installation

Install this package as a dependency using Composer.

```bash
composer require cycle/entity-behavior-uuid
```

## Example

They are randomly-generated and do not contain any information about the time they are created or the machine that
generated them.

```php
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\ORM\Entity\Behavior\Uuid\Uuid4;
use Ramsey\Uuid\UuidInterface;

#[Entity]
#[Uuid4]
class User
{
    #[Column(field: 'uuid', type: 'uuid', primary: true)]
    private UuidInterface $uuid;
}
```

You can find more information about Entity behavior UUID [here](https://cycle-orm.dev/docs/entity-behaviors-uuid).

## License:

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information.
Maintained by [Spiral Scout](https://spiralscout.com).
